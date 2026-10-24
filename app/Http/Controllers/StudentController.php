<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Classroom;
use App\Models\Intervention;
use App\Models\Student;
use App\Services\StudentNarrativeService;
use App\Services\StudentNumberGenerator;
use App\Services\TeacherScopeService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request, TeacherScopeService $scope)
    {
        $query = Student::where('school_id', $request->user()->school_id)
            ->with([
                'enrollments' => fn($q) => $q->whereHas('academicYear', fn($y) => $y->where('is_current', true)),
                'termResults',
            ]);

        $classroomIds = $scope->accessibleClassroomIds($request->user());
        if ($classroomIds !== null) {
            $query->whereHas('enrollments', fn($q) => $q->whereIn('class_id', $classroomIds));
        }

        if ($search = $request->get('search')) {
            $query->where(fn($q) => $q
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('student_number', 'like', "%{$search}%"));
        }

        if ($classId = $request->get('class')) {
            $query->whereHas('enrollments', fn($q) => $q->where('class_id', $classId));
        }

        if ($status = $request->get('status')) {
            if ($status === 'risk') {
                $query->whereHas('termResults', fn($q) => $q->where('overall_percentage', '<', 50));
            } elseif ($status === 'good') {
                $query->whereDoesntHave('termResults', fn($q) => $q->where('overall_percentage', '<', 50));
            }
        }

        $students = $query->paginate(20)->through(fn($s) => (object) [
            'id' => $s->id,
            'student_number' => $s->student_number,
            'name' => $s->full_name,
            'class' => $s->currentClassroom()?->name ?? '—',
            'average' => $s->termResults->last()?->overall_percentage ?? 0,
            'position' => $s->termResults->last()
                ? "{$s->termResults->last()->position}/{$s->termResults->last()->class_size}"
                : '—',
            'attendance' => ($s->termResults->last()?->attendance_percentage ?? 0) . '%',
            'status' => ($s->termResults->last()?->overall_percentage ?? 100) < 50 ? 'risk' : 'good',
        ]);

        $classrooms = Classroom::where('school_id', $request->user()->school_id)
            ->when($classroomIds !== null, fn($q) => $q->whereIn('id', $classroomIds))
            ->orderBy('name')
            ->get();

        return view('students.index', [
            'students' => $students,
            'classrooms' => $classrooms,
        ]);
    }

    public function create(Request $request, TeacherScopeService $scope)
    {
        $classroomIds = $scope->accessibleClassroomIds($request->user());

        $classrooms = Classroom::where('school_id', $request->user()->school_id)
            ->when($classroomIds !== null, fn($q) => $q->whereIn('id', $classroomIds))
            ->get();

        return view('students.create', compact('classrooms'));
    }

    public function store(Request $request, StudentNumberGenerator $numberGenerator, TeacherScopeService $scope)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'class_id' => 'required|exists:classes,id',
        ]);

        abort_unless(
            $scope->canAccessClassroom($request->user(), (int) $data['class_id']),
            403,
            'You cannot enroll students into this class.'
        );

        $school = $request->user()->school;

        $student = Student::create([
            'school_id' => $school->id,
            'student_number' => $numberGenerator->next($school),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'status' => 'active',
        ]);

        $student->enrollments()->create([
            'class_id' => $data['class_id'],
            'academic_year_id' => $school->currentAcademicYear()->id,
            'status' => 'active',
        ]);

        return redirect()->route('students.index')->with('success', "{$student->full_name} added successfully.");
    }

    public function show(Student $student, StudentNarrativeService $narrativeService, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessStudent(auth()->user(), $student),
            403,
            'You do not have access to this student.'
        );

        $student->load('guardians');

        $currentTerm = $student->school->currentAcademicYear()?->currentTerm();

        // ---------- Subject results ----------
        $subjectResults = $currentTerm
            ? $student->subjectTermResults()->where('term_id', $currentTerm->id)->with('subject')->get()
            : collect();

        $subjects = $subjectResults->map(fn($r) => [
            $r->subject->name,
            $r->final_percentage,
            ($r->change_from_previous >= 0 ? '+' : '') . $r->change_from_previous,
            $r->trendDirection(),
        ]);

        $termResult = $currentTerm
            ? $student->termResults()->where('term_id', $currentTerm->id)->first()
            : null;

        [$strengths, $weaknesses] = $currentTerm
            ? $narrativeService->strengthsAndWeaknesses($student, $currentTerm)
            : [[], []];

        $learningReport = $currentTerm
            ? $narrativeService->learningReport($student, $termResult, $subjectResults)
            : "{$student->full_name} has not been assigned to a current term yet.";

        // ---------- Header stats object ----------
        $studentData = (object) [
            'id' => $student->id,
            'student_number' => $student->student_number,
            'name' => $student->full_name,
            'class' => $student->currentClassroom()?->name ?? '—',
            'average' => $termResult?->overall_percentage ?? 0,
            'position' => $termResult ? "{$termResult->position}/{$termResult->class_size}" : '—',
            'attendance' => $termResult?->attendance_percentage ?? 0,
            'photo_url' => $student->photoUrl(),
            'achievement_level' => $termResult?->achievement_level ?? 'Not yet assessed',
            'has_portal_access' => (bool) $student->user_id,
        ];

        // ---------- Risk detection ----------
        $risk = $this->computeRisk($student, $currentTerm, $subjectResults);

        // ---------- Intervention history ----------
        $interventions = Intervention::where('student_id', $student->id)
            ->with('subject')
            ->latest('start_date')
            ->limit(6)
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'area' => $i->area,
                'type' => str_replace('_', ' ', $i->intervention_type),
                'status' => $i->status,
                'priority' => $i->priority,
                'started' => $i->start_date?->format('d M') ?? '—',
                'review' => $i->review_date?->format('d M') ?? '—',
                'score_note' => $i->score_after !== null
                    ? "{$i->score_before}% to {$i->score_after}%"
                    : "Target {$i->score_target}%",
            ])->all();

        // ---------- Missing assessments ----------
        $missingAssessments = $this->missingAssessments($student, $currentTerm);

        return view('students.profile', [
            'student' => $studentData,
            'studentModel' => $student,
            'subjects' => $subjects,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'learningReport' => $learningReport,
            'risk' => $risk,
            'interventions' => $interventions,
            'guardians' => $student->guardians,
            'missingAssessments' => $missingAssessments,
            'currentTerm' => $currentTerm,
            'studentModelId' => $student->id,
        ]);
    }

    public function edit(Student $student)
    {
        $classrooms = Classroom::where('school_id', $student->school_id)->get();
        return view('students.edit', compact('student', 'classrooms'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $student->update($data);

        return redirect()->route('students.show', $student)->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->update(['status' => 'inactive']);

        return redirect()->route('students.index')->with('success', 'Student marked inactive.');
    }

    /**
     * Compute live risk level + reasons from real data.
     */
    private function computeRisk(Student $student, $currentTerm, $subjectResults): array
    {
        $reasons = [];

        foreach ($subjectResults as $r) {
            if ($r->final_percentage < 50) {
                $reasons[] = "{$r->subject->name} is below 50% ({$r->final_percentage}%)";
            }
        }

        $recent = AssessmentResult::where('student_id', $student->id)
            ->latest('id')->limit(6)->pluck('percentage')->all();

        $declines = 0;
        for ($i = 0; $i < count($recent) - 1; $i++) {
            if ($recent[$i] < $recent[$i + 1]) {
                $declines++;
            }
        }
        if ($declines >= 2) {
            $reasons[] = "Declining across last {$declines} assessments";
        }

        $missing = $this->missingAssessments($student, $currentTerm);
        if ($missing->isNotEmpty()) {
            $reasons[] = "{$missing->count()} assessment(s) not marked";
        }

        $level = count($reasons) >= 2 ? 'at_risk' : (count($reasons) === 1 ? 'watch' : 'ok');

        return ['level' => $level, 'reasons' => $reasons];
    }

    /**
     * Expected vs. recorded assessments for the current term.
     */
    private function missingAssessments(Student $student, $currentTerm)
    {
        if (!$currentTerm) {
            return collect();
        }

        $classroom = $student->currentClassroom();
        if (!$classroom) {
            return collect();
        }

        $expectedIds = Assessment::where('class_id', $classroom->id)
            ->where('term_id', $currentTerm->id)
            ->pluck('id');

        $markedIds = AssessmentResult::where('student_id', $student->id)
            ->whereIn('assessment_id', $expectedIds)
            ->pluck('assessment_id');

        return Assessment::whereIn('id', $expectedIds->diff($markedIds))
            ->with('subject')
            ->get();
    }
}