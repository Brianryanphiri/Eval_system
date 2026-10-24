<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentLearningProfile;
use App\Models\Term;
use App\Services\LearningProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningFlagController extends Controller
{
    /**
     * List every flagged student in the current term.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        // All terms for the selector
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

        // All classes for the filter
        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id', 'all');
        $selectedClass = $selectedClassId !== 'all'
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        // Flag filter
        $flagFilter = $request->query('flag', 'all');

        // Base query
        $query = StudentLearningProfile::with(['student', 'term'])
            ->where('school_id', $school->id)
            ->when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id))
            ->when($flagFilter !== 'all', fn($q) => $q->where('flag', $flagFilter))
            ->when($selectedClass, function ($q) use ($selectedClass, $currentYear) {
                $q->whereHas('student.enrollments', function ($eq) use ($selectedClass, $currentYear) {
                    $eq->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear?->id)
                        ->where('status', 'active');
                });
            });

        // Stats for the KPI bar
        $baseForStats = StudentLearningProfile::where('school_id', $school->id)
            ->when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id));

        $flagCounts = (clone $baseForStats)
            ->select('flag', DB::raw('COUNT(*) as c'))
            ->groupBy('flag')
            ->pluck('c', 'flag')
            ->toArray();

        $totalFlagged = array_sum($flagCounts);
        $criticalCount = ($flagCounts['at_risk'] ?? 0) + ($flagCounts['needs_support'] ?? 0);

        // Sorting
        $sort = $request->query('sort', 'severity');
        $query->when($sort === 'severity', function ($q) {
            $q->orderByRaw("FIELD(flag,
                'at_risk',
                'needs_support',
                'watch',
                'uneven',
                'improving',
                'high_achiever',
                'on_track')");
        })
            ->when($sort === 'name', function ($q) {
                $q->join('students', 'students.id', '=', 'student_learning_profiles.student_id')
                    ->orderBy('students.last_name')
                    ->orderBy('students.first_name')
                    ->select('student_learning_profiles.*');
            })
            ->when($sort === 'average_low', function ($q) {
                $q->orderBy('overall_score');
            })
            ->when($sort === 'average_high', function ($q) {
                $q->orderByDesc('overall_score');
            })
            ->when($sort === 'change_low', function ($q) {
                $q->orderBy('overall_delta');
            })
            ->when($sort === 'change_high', function ($q) {
                $q->orderByDesc('overall_delta');
            });

        $profiles = $query->paginate(30)->withQueryString();

        // Known flag list for the filter tabs
        $flagMeta = [
            'at_risk' => ['label' => 'At Risk', 'color' => 'red'],
            'needs_support' => ['label' => 'Needs Support', 'color' => 'amber'],
            'watch' => ['label' => 'Watch', 'color' => 'amber'],
            'uneven' => ['label' => 'Uneven', 'color' => 'amber'],
            'improving' => ['label' => 'Improving', 'color' => 'green'],
            'high_achiever' => ['label' => 'High Achiever', 'color' => 'green'],
            'on_track' => ['label' => 'On Track', 'color' => 'green'],
        ];

        return view('learning.flags', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'flagFilter' => $flagFilter,
            'flagCounts' => $flagCounts,
            'totalFlagged' => $totalFlagged,
            'criticalCount' => $criticalCount,
            'profiles' => $profiles,
            'flagMeta' => $flagMeta,
            'sort' => $sort,
        ]);
    }

    /**
     * Show a single student's learning profile detail.
     */
    public function show(Student $student)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        $school = auth()->user()->school;

        // All profiles for this student, newest first
        $profiles = StudentLearningProfile::with('term.academicYear')
            ->where('student_id', $student->id)
            ->orderByDesc('computed_at')
            ->get();

        $latest = $profiles->first();

        // Subject breakdown — best/worst/deltas
        $subjectBreakdown = collect();
        if ($latest) {
            $deltas = $latest->subject_deltas ?? [];
            $subjectIds = array_keys($deltas);

            if (!empty($subjectIds)) {
                $subjects = \App\Models\Subject::whereIn('id', $subjectIds)
                    ->orderBy('name')
                    ->get()
                    ->keyBy('id');

                // Current term results for those subjects
                $current = \App\Models\SubjectTermResult::where('student_id', $student->id)
                    ->where('term_id', $latest->term_id)
                    ->get()
                    ->keyBy('subject_id');

                foreach ($subjectIds as $subjectId) {
                    $subject = $subjects->get((int) $subjectId);
                    if (!$subject) {
                        continue;
                    }
                    $r = $current->get($subjectId);

                    $subjectBreakdown->push([
                        'subject' => $subject->name,
                        'score' => $r ? (float) $r->final_percentage : null,
                        'grade' => $r?->grade_letter,
                        'delta' => $deltas[$subjectId],
                    ]);
                }

                // Sort by score descending
                $subjectBreakdown = $subjectBreakdown->sortByDesc('score')->values();
            }
        }

        return view('learning.student', [
            'student' => $student,
            'profiles' => $profiles,
            'latest' => $latest,
            'subjectBreakdown' => $subjectBreakdown,
        ]);
    }

    /**
     * Recompute profiles for the current term.
     */
    public function recompute(Request $request, LearningProfileService $service)
    {
        $user = auth()->user();
        $school = $user->school;

        // Find the term to recompute — from query string or current
        $termId = $request->input('term_id');

        if (!$termId) {
            $currentTerm = Term::whereHas('academicYear', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })
                ->whereHas('academicYear', fn($q) => $q->where('is_current', true))
                ->where('is_current', true)
                ->first();

            $termId = $currentTerm?->id;
        }

        if (!$termId) {
            return back()->with('error', 'No current term set. Configure the academic calendar first.');
        }

        try {
            $count = $service->recomputeForTerm((int) $termId, $school->id);

            return back()->with('success', "Recomputed learning profiles for {$count} students.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Recompute failed: ' . $e->getMessage());
        }
    }
}