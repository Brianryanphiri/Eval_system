<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\GradingScale;
use App\Models\Student;
use App\Models\StudentMark;
use App\Services\AssessmentCalculationEngine;
use App\Services\TeacherScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarkingController extends Controller
{
    public function edit(Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment(auth()->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        $questions = $assessment->questions()->get()
            ->map(fn($q) => ['no' => $q->question_number, 'max' => $q->max_marks, 'id' => $q->id]);

        $studentsList = $assessment->classroom->currentStudents()->get()
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->full_name]);

        return view('marking.entry', compact('assessment', 'questions', 'studentsList'));
    }

    public function store(Request $request, Assessment $assessment, AssessmentCalculationEngine $engine, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment($request->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        if ($assessment->isLocked()) {
            abort(403, 'This assessment is locked. Marks cannot be edited.');
        }

        $data = $request->validate(['marks' => 'required|array']);

        foreach ($data['marks'] as $studentId => $questionMarks) {
            foreach ($questionMarks as $questionNumber => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                $question = $assessment->questions()->where('question_number', $questionNumber)->first();
                if (!$question) {
                    continue;
                }

                StudentMark::updateOrCreate(
                    ['student_id' => $studentId, 'question_id' => $question->id],
                    ['marks_obtained' => $value, 'marked_by' => $request->user()->id]
                );
            }

            $engine->calculateAssessmentResult(Student::find($studentId), $assessment);
        }

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Marks saved successfully.');
    }

    public function lock(Request $request, Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment($request->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        $assessment->update(['status' => 'locked', 'locked_at' => now()]);

        return back()->with('success', 'Assessment locked. Further edits require a correction entry.');
    }

    /**
     * Bulk marks entry — pick an assessment, enter all students' totals at once.
     */
    public function bulkEntry(Request $request, TeacherScopeService $scope)
    {
        $user = auth()->user();
        $school = $user->school;

        // All assessments for this school, latest first
        $assessments = Assessment::where('school_id', $school->id)
            ->with(['classroom', 'subject', 'term'])
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $selectedAssessmentId = $request->query('assessment_id') ?? $assessments->first()?->id;
        $selectedAssessment = $selectedAssessmentId
            ? $assessments->firstWhere('id', (int) $selectedAssessmentId)
            : null;

        // If an assessment is selected, load its students + existing marks
        $students = collect();
        $existingMarks = collect();

        if ($selectedAssessment && $scope->canAccessAssessment($user, $selectedAssessment)) {
            $classroom = $selectedAssessment->classroom;

            if ($classroom) {
                $students = Student::where('school_id', $school->id)
                    ->whereHas('enrollments', function ($q) use ($classroom) {
                        $q->where('class_id', $classroom->id)
                            ->where('status', 'active');
                    })
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->get();

                $existingMarks = AssessmentResult::where('assessment_id', $selectedAssessment->id)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('marking.bulk-entry', [
            'assessments' => $assessments,
            'selectedAssessment' => $selectedAssessment,
            'selectedAssessmentId' => $selectedAssessmentId,
            'students' => $students,
            'existingMarks' => $existingMarks,
        ]);
    }

    /**
     * Save bulk marks for an assessment.
     */
    public function bulkStore(Request $request, AssessmentCalculationEngine $engine, TeacherScopeService $scope)
    {
        $user = auth()->user();

        $data = $request->validate([
            'assessment_id' => 'required|integer|exists:assessments,id',
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
        ]);

        $assessment = Assessment::findOrFail($data['assessment_id']);

        abort_unless(
            $scope->canAccessAssessment($user, $assessment),
            403,
            'You do not have access to this assessment.'
        );

        if ($assessment->isLocked()) {
            return back()->with('error', 'This assessment is locked. Unlock it before editing marks.');
        }

        $maxMark = (float) ($assessment->total_marks ?? 100);
        $saved = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($data['marks'] as $studentId => $rawMark) {
                $studentId = (int) $studentId;

                if ($rawMark === null || $rawMark === '') {
                    continue;
                }

                $mark = (float) $rawMark;

                if ($mark < 0 || $mark > $maxMark) {
                    $errors[] = "Student #{$studentId}: mark must be between 0 and {$maxMark}.";
                    continue;
                }

                $percentage = $maxMark > 0 ? round(($mark / $maxMark) * 100, 2) : 0;

                AssessmentResult::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'assessment_id' => $assessment->id,
                    ],
                    [
                        'total_obtained' => $mark,
                        'percentage' => $percentage,
                        'grade_letter' => $this->gradeLetter($percentage, $assessment->school_id),
                        'passed' => $percentage >= ($assessment->pass_mark ?? 50),
                        'last_modified_by' => $user->id,
                    ]
                );

                try {
                    $engine->calculateAssessmentResult(
                        Student::find($studentId),
                        $assessment
                    );
                } catch (\Throwable $e) {
                    // Continue — engine failure shouldn't kill the whole batch
                }

                $saved++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk save failed: ' . $e->getMessage());
        }

        $msg = "Saved marks for {$saved} " . str('student')->plural($saved) . '.';
        if (!empty($errors)) {
            return back()
                ->with('warning', $msg . ' ' . count($errors) . ' rows were skipped.')
                ->with('bulk_errors', $errors);
        }

        return back()->with('success', $msg);
    }

    /**
     * Convert a percentage to a letter grade for a school's grading scale.
     */
    private function gradeLetter(float $percentage, int $schoolId): ?string
    {
        $band = GradingScale::where('school_id', $schoolId)
            ->where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->first();

        return $band?->letter;
    }
}