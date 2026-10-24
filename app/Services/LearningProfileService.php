<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentLearningProfile;
use App\Models\SubjectTermResult;
use App\Models\Term;
use App\Models\TermResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LearningProfileService
{
    /**
     * Compute learning profiles for every active student in the given school + term.
     * Returns the number of students processed.
     */
    public function recomputeForTerm(int $termId, int $schoolId): int
    {
        $term = Term::findOrFail($termId);

        // Get every student who has results this term
        $studentIds = SubjectTermResult::where('term_id', $termId)
            ->whereHas('student', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->distinct()
            ->pluck('student_id');

        $count = 0;
        foreach ($studentIds as $studentId) {
            $student = Student::find($studentId);
            if (!$student) {
                continue;
            }

            $this->recomputeForStudent($student, $termId);
            $count++;
        }

        return $count;
    }

    /**
     * Compute a single student's learning profile for a term.
     */
    public function recomputeForStudent(Student $student, int $termId): StudentLearningProfile
    {
        // 1. Pull this term's subject results
        $thisTerm = SubjectTermResult::where('student_id', $student->id)
            ->where('term_id', $termId)
            ->get();

        // 2. Peer comparison — same class, same term
        $peerAvg = $this->peerAverage($student, $termId);
        $peerStddev = $this->peerStddev($student, $termId);

        // 3. Previous term's results (for delta)
        $previousTerm = $this->getPreviousTerm($termId, $student->school_id);
        $prevResults = $previousTerm
            ? SubjectTermResult::where('student_id', $student->id)
                ->where('term_id', $previousTerm->id)
                ->get()
                ->keyBy('subject_id')
            : collect();

        // 4. Overall stats
        $scores = $thisTerm->pluck('final_percentage')->map(fn($v) => (float) $v)->values();
        $overall = $scores->count() > 0 ? round($scores->avg(), 2) : 0;
        $spread = $scores->count() > 0 ? round($scores->max() - $scores->min(), 2) : 0;
        $volatility = $this->stddev($scores->toArray());

        // 5. Previous overall
        $prevOverall = $prevResults->count() > 0
            ? round($prevResults->avg('final_percentage'), 2)
            : null;
        $overallDelta = $prevOverall !== null ? round($overall - $prevOverall, 2) : null;

        // 6. Per-subject delta (this term vs previous term)
        $subjectDeltas = [];
        foreach ($thisTerm as $result) {
            $prev = $prevResults->get($result->subject_id);
            $subjectDeltas[(string) $result->subject_id] = $prev
                ? round((float) $result->final_percentage - (float) $prev->final_percentage, 2)
                : null;
        }

        // 7. Pass mark (from grading scale if available, default 50)
        $passMark = 50.0;

        // 8. Z-score
        $zScore = $peerStddev > 0
            ? round(($overall - $peerAvg) / $peerStddev, 2)
            : 0;

        // 9. Classify
        $flag = $this->classify(
            $overall,
            $overallDelta,
            $spread,
            $volatility,
            $peerAvg,
            $passMark,
            $subjectDeltas,
            $zScore
        );

        // 10. Persist
        return StudentLearningProfile::updateOrCreate(
            [
                'student_id' => $student->id,
                'term_id' => $termId,
            ],
            [
                'school_id' => $student->school_id,
                'overall_score' => $overall,
                'peer_average' => $peerAvg,
                'z_score' => $zScore,
                'spread' => $spread,
                'volatility' => $volatility,
                'overall_delta' => $overallDelta,
                'flag' => $flag,
                'subject_deltas' => $subjectDeltas,
                'computed_at' => now(),
            ]
        );
    }

    /**
     * The classification rules — the heart of the system.
     * Returns one of: at_risk, needs_support, watch, uneven, improving, high_achiever, on_track.
     */
    protected function classify(
        float $overall,
        ?float $delta,
        float $spread,
        float $volatility,
        float $peerAvg,
        float $passMark,
        array $subjectDeltas,
        float $zScore
    ): string {
        // 🔴 At risk: failing badly OR sharp decline OR very volatile while failing
        if ($overall < $passMark - 10) {
            return 'at_risk';
        }
        if ($delta !== null && $delta <= -10) {
            return 'at_risk';
        }
        if ($overall < $passMark && $volatility > 15) {
            return 'at_risk';
        }

        // 🟠 Needs support: below pass mark OR significant subject decline
        if ($overall < $passMark) {
            return 'needs_support';
        }
        foreach ($subjectDeltas as $d) {
            if ($d !== null && $d <= -12) {
                return 'needs_support';
            }
        }
        if ($zScore <= -1.5) {
            return 'needs_support';
        }

        // 🧩 Uneven: wide spread but overall passing
        if ($spread >= 35 && $overall >= $passMark) {
            return 'uneven';
        }

        // ⭐ High achiever: well above peers
        if ($zScore >= 1.5 && ($delta === null || $delta >= -2)) {
            return 'high_achiever';
        }

        // 📈 Improving: meaningful gain
        if ($delta !== null && $delta >= 5) {
            return 'improving';
        }

        // 🟡 Watch: slight decline
        if ($delta !== null && $delta <= -3) {
            return 'watch';
        }

        // Small decline
        if ($delta !== null && $delta <= -1.5) {
            return 'watch';
        }

        // 🟢 On track
        return 'on_track';
    }

    // ---------- Helpers ----------

    protected function getPreviousTerm(int $termId, int $schoolId): ?Term
    {
        $current = Term::find($termId);
        if (!$current) {
            return null;
        }

        return Term::whereHas('academicYear', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })
            ->where(function ($q) use ($current) {
                $q->where('academic_year_id', '<', $current->academic_year_id)
                    ->orWhere(function ($q2) use ($current) {
                        $q2->where('academic_year_id', $current->academic_year_id)
                            ->where('sequence', '<', $current->sequence);
                    });
            })
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->first();
    }

    /**
     * Average of the student's classmates for this term (all subject results averaged).
     */
    protected function peerAverage(Student $student, int $termId): float
    {
        $classroomId = $this->getCurrentClassroomId($student);
        if (!$classroomId) {
            return 0;
        }

        // Classmates via enrollments
        $peerIds = DB::table('enrollments')
            ->join('academic_years', 'academic_years.id', '=', 'enrollments.academic_year_id')
            ->where('enrollments.class_id', $classroomId)
            ->where('academic_years.is_current', true)
            ->where('enrollments.status', 'active')
            ->where('enrollments.student_id', '!=', $student->id)
            ->pluck('enrollments.student_id');

        if ($peerIds->isEmpty()) {
            return 0;
        }

        $avg = SubjectTermResult::where('term_id', $termId)
            ->whereIn('student_id', $peerIds)
            ->avg('final_percentage');

        return $avg !== null ? round((float) $avg, 2) : 0;
    }

    /**
     * Standard deviation of classmates' scores for this term.
     */
    protected function peerStddev(Student $student, int $termId): float
    {
        $classroomId = $this->getCurrentClassroomId($student);
        if (!$classroomId) {
            return 0;
        }

        $peerIds = DB::table('enrollments')
            ->join('academic_years', 'academic_years.id', '=', 'enrollments.academic_year_id')
            ->where('enrollments.class_id', $classroomId)
            ->where('academic_years.is_current', true)
            ->where('enrollments.status', 'active')
            ->where('enrollments.student_id', '!=', $student->id)
            ->pluck('enrollments.student_id');

        if ($peerIds->isEmpty()) {
            return 0;
        }

        // Per-student averages, then stddev of those
        $perStudent = SubjectTermResult::where('term_id', $termId)
            ->whereIn('student_id', $peerIds)
            ->select('student_id', DB::raw('AVG(final_percentage) as avg_score'))
            ->groupBy('student_id')
            ->pluck('avg_score')
            ->map(fn($v) => (float) $v)
            ->toArray();

        return $this->stddev($perStudent);
    }

    protected function getCurrentClassroomId(Student $student): ?int
    {
        return DB::table('enrollments')
            ->join('academic_years', 'academic_years.id', '=', 'enrollments.academic_year_id')
            ->where('enrollments.student_id', $student->id)
            ->where('academic_years.is_current', true)
            ->where('enrollments.status', 'active')
            ->value('enrollments.class_id');
    }

    protected function stddev(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0;
        }

        $mean = array_sum($values) / $n;
        $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $values)) / $n;

        return round(sqrt($variance), 2);
    }
}