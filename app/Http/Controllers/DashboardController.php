<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentLearningProfile;
use App\Models\Subject;
use App\Models\SubjectTermResult;
use App\Models\Term;
use App\Models\TermResult;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $school = $user->school;

        if (!$school) {
            return view('dashboard.index', $this->emptyData());
        }

        $currentYear = AcademicYear::where('school_id', $school->id)
            ->where('is_current', true)
            ->first();

        $currentTerm = $currentYear
            ? Term::where('academic_year_id', $currentYear->id)->where('is_current', true)->first()
            : null;

        $previousTerm = null;
        if ($currentYear && $currentTerm) {
            $previousTerm = Term::where('academic_year_id', $currentYear->id)
                ->where('sequence', '<', $currentTerm->sequence)
                ->orderByDesc('sequence')
                ->first();

            if (!$previousTerm) {
                $prevYear = AcademicYear::where('school_id', $school->id)
                    ->where('id', '<', $currentYear->id)
                    ->orderByDesc('id')
                    ->first();

                if ($prevYear) {
                    $previousTerm = Term::where('academic_year_id', $prevYear->id)
                        ->orderByDesc('sequence')
                        ->first();
                }
            }
        }

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        // ---- STAT CARDS ----
        $totalStudents = Student::where('school_id', $school->id)
            ->where('status', 'active')
            ->count();

        $prevTotalStudents = null;
        if ($currentYear) {
            $prevTotalStudents = DB::table('enrollments')
                ->join('students', 'students.id', '=', 'enrollments.student_id')
                ->where('students.school_id', $school->id)
                ->where('enrollments.academic_year_id', $currentYear->id)
                ->where('enrollments.status', 'active')
                ->distinct()
                ->count('enrollments.student_id');
        }

        $overallAverage = null;
        $previousAverage = null;
        $passRate = null;
        $previousPassRate = null;
        $atRiskCount = 0;
        $previousAtRiskCount = 0;

        $studentIdSubquery = function ($q) use ($school) {
            $q->select('id')->from('students')->where('school_id', $school->id);
        };

        if ($currentTerm) {
            $avg = TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->avg('overall_percentage');

            $overallAverage = $avg !== null ? round((float) $avg, 1) : null;

            $total = TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->count();

            if ($total > 0) {
                $passed = TermResult::where('term_id', $currentTerm->id)
                    ->whereIn('student_id', $studentIdSubquery)
                    ->where('overall_percentage', '>=', 50)
                    ->count();

                $passRate = round(($passed / $total) * 100, 1);
            }

            $atRiskCount = StudentLearningProfile::where('school_id', $school->id)
                ->where('term_id', $currentTerm->id)
                ->whereIn('flag', ['at_risk', 'needs_support'])
                ->count();
        }

        if ($previousTerm) {
            $avg = TermResult::where('term_id', $previousTerm->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->avg('overall_percentage');

            $previousAverage = $avg !== null ? round((float) $avg, 1) : null;

            $total = TermResult::where('term_id', $previousTerm->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->count();

            if ($total > 0) {
                $passed = TermResult::where('term_id', $previousTerm->id)
                    ->whereIn('student_id', $studentIdSubquery)
                    ->where('overall_percentage', '>=', 50)
                    ->count();

                $previousPassRate = round(($passed / $total) * 100, 1);
            }

            $previousAtRiskCount = StudentLearningProfile::where('school_id', $school->id)
                ->where('term_id', $previousTerm->id)
                ->whereIn('flag', ['at_risk', 'needs_support'])
                ->count();
        }

        $deltas = [
            'students' => $this->delta($prevTotalStudents, $totalStudents),
            'average' => $this->delta($previousAverage, $overallAverage),
            'pass_rate' => $this->delta($previousPassRate, $passRate),
            'at_risk' => $this->delta($previousAtRiskCount, $atRiskCount, true),
        ];

        // ---- ATTENDANCE RING ----
        $attendanceRing = null;
        if ($currentTerm) {
            $rows = DB::table('attendance_summaries')
                ->where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->selectRaw('SUM(days_present) as present, SUM(days_absent) as absent, SUM(days_late) as late, SUM(days_excused) as excused')
                ->first();

            if ($rows && ((int) $rows->present + (int) $rows->absent + (int) $rows->late + (int) $rows->excused) > 0) {
                $present = (int) $rows->present + (int) $rows->late;
                $total = $present + (int) $rows->absent + (int) $rows->excused;

                $attendanceRing = [
                    'present' => (int) $rows->present,
                    'absent' => (int) $rows->absent,
                    'late' => (int) $rows->late,
                    'excused' => (int) $rows->excused,
                    'percentage' => round(($present / $total) * 100, 0),
                ];
            }
        }

        // ---- TREND CHART ----
        $recentTerms = Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        $trendLabels = [];
        $trendValues = [];
        $trendPassRate = [];

        foreach ($recentTerms as $t) {
            $avg = TermResult::where('term_id', $t->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->avg('overall_percentage');

            if ($avg === null) {
                continue;
            }

            $total = TermResult::where('term_id', $t->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->count();

            $passed = TermResult::where('term_id', $t->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->where('overall_percentage', '>=', 50)
                ->count();

            $trendLabels[] = ($t->name ?? 'Term') . ' ' . ($t->academicYear?->label ?? '');
            $trendValues[] = round((float) $avg, 1);
            $trendPassRate[] = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
        }

        // ---- SUBJECT PERFORMANCE ----
        $subjectsAttention = [];
        if ($currentTerm) {
            $subjectAverages = SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->with('subject')
                ->get()
                ->groupBy('subject_id');

            foreach ($subjectAverages as $subjectId => $results) {
                $subject = $results->first()->subject;
                if (!$subject)
                    continue;

                $avg = round($results->avg('final_percentage'), 1);
                $level = $avg < 50 ? 'low' : ($avg < 65 ? 'mid' : 'ok');

                $subjectsAttention[] = [
                    'name' => $subject->name,
                    'score' => $avg,
                    'level' => $level,
                ];
            }

            usort($subjectsAttention, fn($a, $b) => $a['score'] <=> $b['score']);
            $subjectsAttention = array_slice($subjectsAttention, 0, 5);
        }

        // ---- CLASS COMPARISON ----
        $classComparison = [];
        if ($currentTerm) {
            foreach ($classes as $class) {
                $avg = TermResult::where('term_id', $currentTerm->id)
                    ->where('class_id', $class->id)
                    ->avg('overall_percentage');

                if ($avg !== null) {
                    $classComparison[$class->name] = round((float) $avg, 1);
                }
            }
        }

        // ---- TOP PERFORMERS ----
        $topStudents = [];
        if ($currentTerm) {
            $top = TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIdSubquery)
                ->orderByDesc('overall_percentage')
                ->limit(4)
                ->get();

            $topStudentIds = $top->pluck('student_id')->all();
            $topStudentModels = Student::whereIn('id', $topStudentIds)->get()->keyBy('id');

            foreach ($top as $tr) {
                $student = $topStudentModels->get($tr->student_id);
                if (!$student)
                    continue;

                $topStudents[] = [
                    'name' => $student->full_name,
                    'class' => $student->currentClassroom()?->name ?? '—',
                    'score' => (int) round((float) $tr->overall_percentage),
                ];
            }
        }

        // ---- AT-RISK STUDENTS ----
        $atRiskStudents = [];
        if ($currentTerm) {
            $atRisk = StudentLearningProfile::where('school_id', $school->id)
                ->where('term_id', $currentTerm->id)
                ->whereIn('flag', ['at_risk', 'needs_support'])
                ->orderBy('overall_score')
                ->limit(4)
                ->get();

            $atRiskStudentIds = $atRisk->pluck('student_id')->all();
            $atRiskStudentModels = Student::whereIn('id', $atRiskStudentIds)->get()->keyBy('id');

            foreach ($atRisk as $profile) {
                $student = $atRiskStudentModels->get($profile->student_id);
                if (!$student)
                    continue;

                $atRiskStudents[] = [
                    'name' => $student->full_name,
                    'reason' => $profile->flag === 'at_risk' ? 'Critical — needs support' : 'Below average',
                    'class' => $student->currentClassroom()?->name ?? '—',
                ];
            }
        }

        // ---- RECENTLY ADDED STUDENTS ----
        $recentStudents = Student::where('school_id', $school->id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get()
            ->map(fn($s) => [
                'name' => $s->full_name,
                'class' => $s->currentClassroom()?->name ?? '—',
                'when' => $s->created_at?->diffForHumans() ?? '—',
            ])
            ->all();

        // ---- UPCOMING ASSESSMENTS ----
        $upcomingAssessments = [];
        if ($currentTerm) {
            $upcoming = Assessment::where('school_id', $school->id)
                ->where('term_id', $currentTerm->id)
                ->where('status', '!=', 'locked')
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();

            $classIds = $upcoming->pluck('class_id')->filter()->unique()->all();
            $subjectIds = $upcoming->pluck('subject_id')->filter()->unique()->all();

            $classLookup = Classroom::whereIn('id', $classIds)->get()->keyBy('id');
            $subjectLookup = Subject::whereIn('id', $subjectIds)->get()->keyBy('id');

            foreach ($upcoming as $a) {
                $upcomingAssessments[] = [
                    'name' => $a->name ?? 'Assessment',
                    'class' => $classLookup->get($a->class_id)?->name ?? '—',
                    'subject' => $subjectLookup->get($a->subject_id)?->name ?? '—',
                    'when' => $a->updated_at?->diffForHumans() ?? 'Recently',
                ];
            }
        }

        // ---- RECENT ACTIVITY ----
        $activities = [];
        if ($currentTerm) {
            $recent = Assessment::where('school_id', $school->id)
                ->where('term_id', $currentTerm->id)
                ->orderByDesc('updated_at')
                ->limit(4)
                ->get();

            $recentClassIds = $recent->pluck('class_id')->filter()->unique()->all();
            $recentSubjectIds = $recent->pluck('subject_id')->filter()->unique()->all();

            $classLookup = Classroom::whereIn('id', $recentClassIds)->get()->keyBy('id');
            $subjectLookup = Subject::whereIn('id', $recentSubjectIds)->get()->keyBy('id');

            foreach ($recent as $a) {
                $status = $a->status === 'locked' ? 'locked' : 'submitted';
                $icon = $status === 'locked' ? 'fa-file-circle-check' : 'fa-file-lines';
                $color = $status === 'locked' ? 'green' : 'amber';

                $className = $classLookup->get($a->class_id)?->name ?? '—';
                $subjectName = $subjectLookup->get($a->subject_id)?->name ?? '—';

                $activities[] = [
                    'color' => $color,
                    'icon' => $icon,
                    'text' => '<strong>' . e($a->name ?? 'Assessment') . '</strong> · ' . e($className) . ' · ' . e($subjectName),
                    'when' => $a->updated_at?->diffForHumans() ?? 'Recently',
                ];
            }
        }

        if (empty($activities)) {
            $activities[] = [
                'color' => 'green',
                'icon' => 'fa-circle-info',
                'text' => 'No recent activity yet',
                'when' => 'Today',
            ];
        }

        return view('dashboard.index', [
            'totalStudents' => $totalStudents,
            'overallAverage' => $overallAverage !== null ? $overallAverage . '%' : '—',
            'passRate' => $passRate !== null ? $passRate . '%' : '—',
            'atRiskCount' => $atRiskCount,
            'deltas' => $deltas,
            'attendanceRing' => $attendanceRing,
            'passRateValue' => $passRate,
            'trendLabels' => $trendLabels,
            'trendValues' => $trendValues,
            'trendPassRate' => $trendPassRate,
            'subjectsAttention' => $subjectsAttention,
            'classComparison' => $classComparison,
            'topStudents' => $topStudents,
            'atRiskStudents' => $atRiskStudents,
            'recentStudents' => $recentStudents,
            'upcomingAssessments' => $upcomingAssessments,
            'activities' => $activities,
        ]);
    }

    private function delta($previous, $current, bool $invert = false): array
    {
        if ($previous === null || $current === null || $previous == 0) {
            return ['value' => null, 'direction' => 'flat', 'invert' => $invert];
        }

        $change = (($current - $previous) / $previous) * 100;

        if (abs($change) < 0.05) {
            return ['value' => 0.0, 'direction' => 'flat', 'invert' => $invert];
        }

        return [
            'value' => round(abs($change), 1),
            'direction' => $change > 0 ? 'up' : 'down',
            'invert' => $invert,
        ];
    }

    private function emptyData(): array
    {
        return [
            'totalStudents' => 0,
            'overallAverage' => '—',
            'passRate' => '—',
            'atRiskCount' => 0,
            'deltas' => [
                'students' => ['value' => null, 'direction' => 'flat', 'invert' => false],
                'average' => ['value' => null, 'direction' => 'flat', 'invert' => false],
                'pass_rate' => ['value' => null, 'direction' => 'flat', 'invert' => false],
                'at_risk' => ['value' => null, 'direction' => 'flat', 'invert' => true],
            ],
            'attendanceRing' => null,
            'passRateValue' => null,
            'trendLabels' => [],
            'trendValues' => [],
            'trendPassRate' => [],
            'subjectsAttention' => [],
            'classComparison' => [],
            'topStudents' => [],
            'atRiskStudents' => [],
            'recentStudents' => [],
            'upcomingAssessments' => [],
            'activities' => [],
        ];
    }
}