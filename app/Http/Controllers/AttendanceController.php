<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceReason;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Term;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Landing page — pick a class, pick a date, take attendance.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id') ?? $classes->first()?->id;
        $selectedClass = $selectedClassId
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        // Date defaults to today
        $date = $request->query('date') ?? now()->format('Y-m-d');

        // Period defaults to full-day
        $period = $request->query('period') ?? 'full-day';

        $session = null;
        $students = collect();
        $existingRecords = collect();

        if ($selectedClass && $currentTerm) {
            // Get students in this class this year
            $students = Student::where('school_id', $school->id)
                ->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear?->id)
                        ->where('status', 'active');
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

            // Look for an existing session for this class+date+period
            $session = AttendanceSession::where('school_id', $school->id)
                ->where('class_id', $selectedClass->id)
                ->where('date', $date)
                ->where('period', $period)
                ->first();

            // If it exists, load the records
            if ($session) {
                $existingRecords = AttendanceRecord::where('session_id', $session->id)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        // Reasons for the dropdown
        $reasons = AttendanceReason::forSchool($school->id)->get();

        return view('attendance.take', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'date' => $date,
            'period' => $period,
            'session' => $session,
            'students' => $students,
            'existingRecords' => $existingRecords,
            'reasons' => $reasons,
        ]);
    }

    /**
     * Save attendance for a class + date.
     */
    public function store(Request $request, AttendanceService $service)
    {
        $user = auth()->user();
        $school = $user->school;

        $data = $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'term_id' => 'required|integer|exists:terms,id',
            'date' => 'required|date',
            'period' => 'nullable|string|max:32',
            'records' => 'required|array',
            'records.*.status' => 'required|in:present,absent,late,excused,sick',
            'records.*.reason' => 'nullable|string|max:255',
            'records.*.minutes_late' => 'nullable|integer|min:0|max:600',
        ]);

        // Create or fetch the session
        $session = $service->getOrCreateSession(
            $school->id,
            (int) $data['class_id'],
            (int) $data['term_id'],
            $data['date'],
            $data['period'] ?? 'full-day',
            $user->id
        );

        // Save the records
        $count = $service->saveSession($session, $data['records'], $user->id);

        return redirect()
            ->route('attendance.index', [
                'class_id' => $data['class_id'],
                'term' => $data['term_id'],
                'date' => $data['date'],
                'period' => $data['period'] ?? 'full-day',
            ])
            ->with('success', "Attendance saved for {$count} students.");
    }

    /**
     * Summary page — attendance rates per class + student breakdown.
     */
    public function summary(Request $request, AttendanceService $service)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id', 'all');
        $selectedClass = $selectedClassId !== 'all'
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $classRates = [];
        $studentRows = [];
        $overallRate = null;

        if ($currentTerm) {
            // Per-class rates
            $classRates = $service->getClassAttendanceRates($school->id, $currentTerm->id);

            // Overall rate for the school
            if (!empty($classRates)) {
                $overallRate = round(array_sum($classRates) / count($classRates), 1);
            }

            // Per-student rows — from attendance_summaries
            $query = DB::table('attendance_summaries')
                ->join('students', 'students.id', '=', 'attendance_summaries.student_id')
                ->where('attendance_summaries.school_id', $school->id)
                ->where('attendance_summaries.term_id', $currentTerm->id)
                ->select(
                    'attendance_summaries.*',
                    'students.first_name',
                    'students.last_name',
                    'students.student_number'
                );

            if ($selectedClass) {
                $studentIds = Student::where('school_id', $school->id)
                    ->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                        $q->where('class_id', $selectedClass->id)
                            ->where('academic_year_id', $currentYear?->id)
                            ->where('status', 'active');
                    })
                    ->pluck('id');

                $query->whereIn('attendance_summaries.student_id', $studentIds);
            }

            $studentRows = $query
                ->orderBy('attendance_summaries.percentage')
                ->limit(200)
                ->get();
        }

        return view('attendance.summary', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'classRates' => $classRates,
            'overallRate' => $overallRate,
            'studentRows' => $studentRows,
        ]);
    }

    /**
     * Per-student attendance detail page.
     */
    public function student(Student $student, AttendanceService $service)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        $school = auth()->user()->school;

        // All summaries for this student, per term
        $summaries = DB::table('attendance_summaries')
            ->join('terms', 'terms.id', '=', 'attendance_summaries.term_id')
            ->leftJoin('academic_years', 'academic_years.id', '=', 'terms.academic_year_id')
            ->where('attendance_summaries.school_id', $school->id)
            ->where('attendance_summaries.student_id', $student->id)
            ->select(
                'attendance_summaries.*',
                'terms.name as term_name',
                'academic_years.label as year_label'
            )
            ->orderByDesc('attendance_summaries.term_id')
            ->get();

        // Recent records
        $recent = $service->getStudentRecentRecords($student->id, 30);

        return view('attendance.student', [
            'student' => $student,
            'summaries' => $summaries,
            'recent' => $recent,
        ]);
    }

    /**
     * Manage attendance reasons (settings page).
     */
    public function reasons(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (!in_array($user->role, ['school_admin', 'super_admin'], true)) {
            abort(403);
        }

        $reasons = AttendanceReason::where('school_id', $school->id)
            ->orderBy('label')
            ->get();

        return view('attendance.reasons', compact('reasons'));
    }

    /**
     * Store a new attendance reason.
     */
    public function storeReason(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (!in_array($user->role, ['school_admin', 'super_admin'], true)) {
            abort(403);
        }

        $data = $request->validate([
            'label' => 'required|string|max:255',
            'requires_note' => 'nullable|boolean',
        ]);

        AttendanceReason::create([
            'school_id' => $school->id,
            'label' => $data['label'],
            'requires_note' => $request->boolean('requires_note'),
            'is_active' => true,
        ]);

        return back()->with('success', 'Reason added.');
    }

    /**
     * Delete an attendance reason.
     */
    public function destroyReason(AttendanceReason $reason)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['school_admin', 'super_admin'], true)) {
            abort(403);
        }

        abort_unless($reason->school_id === $user->school_id, 403);

        $reason->delete();

        return back()->with('success', 'Reason removed.');
    }
}