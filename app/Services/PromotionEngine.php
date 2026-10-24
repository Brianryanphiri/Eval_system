<?php

namespace App\Services;

use App\Models\PromotionRule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Support\Collection;

class PromotionEngine
{
    /**
     * Evaluate a student's promotion status against their school's active
     * PromotionRule + conditions — Section 7's exact logic:
     *   Overall average >= X AND [per-subject minimums] AND max failed subjects <= Y
     *
     * Returns [status, humanReadableReason]
     */
    public function evaluate(Student $student, Term $term, Collection $subjectResults): array
    {
        $rule = PromotionRule::where('school_id', $student->school_id)->latest()->first();

        if (!$rule) {
            return ['pending', 'No promotion rule configured for this school yet.'];
        }

        $reasons = [];
        $failed = true; // assume failure until every check passes

        $overallAverage = $subjectResults->isNotEmpty()
            ? round($subjectResults->avg('final_percentage'), 2)
            : 0;

        $failedSubjectsCount = $subjectResults->where('passed', false)->count();

        switch ($rule->rule_type) {
            case 'overall_average':
                $meetsAverage = $rule->min_overall_average === null
                    || $overallAverage >= $rule->min_overall_average;

                if (!$meetsAverage) {
                    $reasons[] = "Overall average {$overallAverage}% is below the required {$rule->min_overall_average}%";
                }

                $failed = !$meetsAverage;
                break;

            case 'all_compulsory':
                $failedCompulsory = $subjectResults->filter(function ($r) {
                    $subject = Subject::find($r->subject_id);
                    return $subject?->is_compulsory && !$r->passed;
                });

                foreach ($failedCompulsory as $r) {
                    $subject = Subject::find($r->subject_id);
                    $reasons[] = "{$subject->name} (compulsory) not passed — {$r->final_percentage}%";
                }

                $failed = $failedCompulsory->isNotEmpty();
                break;

            case 'min_passed':
                $passedCount = $subjectResults->where('passed', true)->count();
                $required = $subjectResults->count() - ($rule->max_failed_subjects ?? 0);

                if ($passedCount < $required) {
                    $reasons[] = "Only {$passedCount} of {$subjectResults->count()} subjects passed, required at least {$required}";
                }

                $failed = $passedCount < $required;
                break;

            default:
                // credit_based / board_decision / custom — flag for manual review
                // rather than pretending an automated verdict, matching Section 21's
                // "don't pretend an algorithm can decide everything" principle
                return ['pending', 'This promotion rule type requires manual academic board review.'];
        }

        // Universal overlays that apply regardless of rule_type, if set
        if ($rule->max_failed_subjects !== null && $failedSubjectsCount > $rule->max_failed_subjects) {
            $reasons[] = "{$failedSubjectsCount} subjects failed, exceeding the maximum of {$rule->max_failed_subjects}";
            $failed = true;
        }

        foreach ($rule->conditions as $condition) {
            $subjectResult = $subjectResults->firstWhere('subject_id', $condition->subject_id);
            if (!$subjectResult) {
                continue;
            }

            $subject = Subject::find($condition->subject_id);
            $meetsCondition = $condition->operator === '>='
                ? $subjectResult->final_percentage >= $condition->value
                : $subjectResult->final_percentage > $condition->value;

            if (!$meetsCondition) {
                $reasons[] = "{$subject->name} {$subjectResult->final_percentage}% does not meet required {$condition->operator} {$condition->value}%";
                $failed = true;
            }
        }

        if (empty($reasons) && !$failed) {
            return ['promoted', 'All promotion conditions met.'];
        }

        // Partial failure with minor margin could be "conditional" — simple heuristic:
        // failed by less than 5 percentage points on the average = conditional, not outright failure
        $isConditional = $rule->min_overall_average !== null
            && $overallAverage >= ($rule->min_overall_average - 5)
            && $overallAverage < $rule->min_overall_average;

        return [
            $isConditional ? 'conditional' : 'not_promoted',
            implode('; ', $reasons),
        ];
    }
}