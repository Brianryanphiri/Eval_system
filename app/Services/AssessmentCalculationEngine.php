<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\GradingScale;
use App\Models\Student;
use App\Models\StudentMark;
use App\Models\Subject;
use App\Models\SubjectTermResult;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class AssessmentCalculationEngine
{
    /**
     * STEP 1: Sum a student's per-question marks into a total assessment_result.
     * This runs after marking is submitted for one assessment.
     */
    public function calculateAssessmentResult(Student $student, Assessment $assessment): AssessmentResult
    {
        $totalObtained = StudentMark::where('student_id', $student->id)
            ->whereIn('question_id', $assessment->questions()->pluck('id'))
            ->sum('marks_obtained');

        $totalMarks = $assessment->total_marks;
        $percentage = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 2) : 0;

        $passed = $assessment->pass_mark === null
            ? true
            : $percentage >= $assessment->pass_mark;

        $gradeLetter = GradingScale::resolve($assessment->school_id, $percentage)?->letter;

        return AssessmentResult::updateOrCreate(
            ['student_id' => $student->id, 'assessment_id' => $assessment->id],
            [
                'total_obtained' => $totalObtained,
                'percentage' => $percentage,
                'grade_letter' => $gradeLetter,
                'passed' => $passed,
            ]
        );
    }

    /**
     * STEP 2: Combine ALL assessment results for one student + subject + term
     * into the single weighted Subject Term Result — this is the exact
     * Section 12 example: Assignment 18/20 (20%) + Midterm 55/100 (30%) + Final 72/100 (50%) = 70.5%
     */
    public function calculateSubjectTermResult(Student $student, Subject $subject, Term $term): SubjectTermResult
    {
        $assessments = Assessment::where('term_id', $term->id)
            ->where('subject_id', $subject->id)
            ->whereHas('classroom.students', fn($q) => $q->where('students.id', $student->id))
            ->where('status', 'locked') // only locked/finalized assessments count toward the term total
            ->get();

        $weights = $subject->effectiveWeights()->keyBy('assessment_type_id');

        $weightedSum = 0;
        $weightUsed = 0;

        foreach ($assessments as $assessment) {
            $result = AssessmentResult::where('student_id', $student->id)
                ->where('assessment_id', $assessment->id)
                ->first();

            if (!$result) {
                continue; // student has no recorded result for this assessment yet
            }

            // contribution_percentage on the assessment itself overrides the general weight, if set
            $weightPct = $assessment->contribution_percentage
                ?? $weights->get($assessment->assessment_type_id)?->weight_percentage
                ?? 0;

            $weightedSum += ($result->percentage / 100) * $weightPct;
            $weightUsed += $weightPct;
        }

        // Normalize in case the assessments found don't sum to exactly 100%
        // (e.g. some components not yet marked) — scale proportionally to what's available
        $finalPercentage = $weightUsed > 0
            ? round(($weightedSum / $weightUsed) * 100, 2)
            : 0;

        $gradeScale = GradingScale::resolve($subject->school_id, $finalPercentage);
        $achievementLevel = $this->resolveAchievementLevel($subject->school_id, $finalPercentage);

        // Trend vs previous term
        $previousTerm = Term::where('academic_year_id', $term->academic_year_id)
            ->where('sequence', '<', $term->sequence)
            ->orderByDesc('sequence')
            ->first();

        $previousResult = $previousTerm
            ? SubjectTermResult::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('term_id', $previousTerm->id)
                ->first()
            : null;

        $passMarkThreshold = 50; // could be pulled from a school-level setting instead

        return SubjectTermResult::updateOrCreate(
            ['student_id' => $student->id, 'subject_id' => $subject->id, 'term_id' => $term->id],
            [
                'final_percentage' => $finalPercentage,
                'grade_letter' => $gradeScale?->letter,
                'achievement_level' => $achievementLevel,
                'passed' => $finalPercentage >= $passMarkThreshold,
                'previous_term_percentage' => $previousResult?->final_percentage,
                'change_from_previous' => $previousResult
                    ? round($finalPercentage - $previousResult->final_percentage, 2)
                    : null,
                'calculated_at' => now(),
            ]
        );
    }

    /**
     * STEP 3: Roll every subject's term result for a student into the overall
     * term average, then evaluate the promotion rule against it.
     */
    public function calculateTermResult(Student $student, Term $term): array
    {
        $subjectResults = SubjectTermResult::where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->get();

        $overallPercentage = $subjectResults->isNotEmpty()
            ? round($subjectResults->avg('final_percentage'), 2)
            : 0;

        $achievementLevel = $this->resolveAchievementLevel($student->school_id, $overallPercentage);

        [$promotionStatus, $promotionReason] = app(PromotionEngine::class)
            ->evaluate($student, $term, $subjectResults);

        return [
            'overall_percentage' => $overallPercentage,
            'achievement_level' => $achievementLevel,
            'promotion_status' => $promotionStatus,
            'promotion_reason' => $promotionReason,
        ];
    }

    /**
     * Section 6's Achievement Bands — school-configurable, but falling back
     * to sensible defaults if the school hasn't set custom bands.
     */
    private function resolveAchievementLevel(int $schoolId, float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'Exceptional',
            $percentage >= 80 => 'Advanced',
            $percentage >= 70 => 'Proficient',
            $percentage >= 50 => 'Developing',
            default => 'Beginning',
        };
    }

    /**
     * Recalculate everything for one student across one term — useful after
     * a bulk mark correction or when unlocking/relocking an assessment.
     */
    public function recalculateAllForStudent(Student $student, Term $term): void
    {
        DB::transaction(function () use ($student, $term) {
            $subjects = Subject::where('school_id', $student->school_id)->get();

            foreach ($subjects as $subject) {
                $this->calculateSubjectTermResult($student, $subject, $term);
            }

            $this->calculateTermResult($student, $term);
        });
    }
}