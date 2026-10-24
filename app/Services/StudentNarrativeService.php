<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Term;
use App\Models\TermResult;
use Illuminate\Support\Collection;

class StudentNarrativeService
{
    /**
     * Builds the strengths and weaknesses lists for a student's current term,
     * based on their actual subject scores rather than fixed sample text.
     */
    public function strengthsAndWeaknesses(Student $student, Term $term): array
    {
        $results = $student->subjectTermResults()
            ->where('term_id', $term->id)
            ->with('subject')
            ->get()
            ->sortByDesc('final_percentage');

        $strengths = $results->filter(fn($r) => $r->final_percentage >= 65)
            ->take(4)
            ->map(fn($r) => $r->subject->name)
            ->values()->all();

        $weaknesses = $results->filter(fn($r) => $r->final_percentage < 50)
            ->sortBy('final_percentage')
            ->take(4)
            ->map(fn($r) => $r->subject->name)
            ->values()->all();

        // If nothing clears either bar, fall back to top/bottom scorer so the
        // panels are never empty just because a student is all mid-range.
        if (empty($strengths) && $results->isNotEmpty()) {
            $strengths = [$results->first()->subject->name];
        }
        if (empty($weaknesses) && $results->isNotEmpty() && $results->count() > 1) {
            $weaknesses = [$results->last()->subject->name];
        }

        return [$strengths, $weaknesses];
    }

    /**
     * Generates the Learning Report paragraph from real term data, matching
     * the style from your doc's Section 44 example, but built from whatever
     * this specific student's numbers actually are.
     */
    public function learningReport(Student $student, ?TermResult $termResult, Collection $subjectResults): string
    {
        if (!$termResult || $subjectResults->isEmpty()) {
            return "{$student->full_name} does not yet have enough recorded results this term to generate a learning report.";
        }

        $level = strtolower($termResult->achievement_level ?? 'developing');
        $sorted = $subjectResults->sortByDesc('final_percentage');
        $strongest = $sorted->first();
        $weakest = $sorted->last();

        $sentences = [];

        $sentences[] = "{$student->full_name} is performing at a " . ucfirst($level) . " level overall.";

        $priorTerm = $subjectResults->pluck('change_from_previous')->filter()->avg();
        if ($priorTerm !== null) {
            $direction = $priorTerm >= 0 ? 'improved' : 'declined';
            $sentences[] = "Overall performance has " . $direction . " by " . abs(round($priorTerm, 1)) . " percentage points compared with the previous term.";
        }

        if ($strongest && $weakest && $strongest->subject_id !== $weakest->subject_id) {
            $sentences[] = "The strongest demonstrated area is {$strongest->subject->name} at {$strongest->final_percentage}%, while {$weakest->subject->name} remains the area most in need of support at {$weakest->final_percentage}%.";
        }

        if ($weakest && $weakest->final_percentage < 50) {
            $sentences[] = "Recommended: targeted support in {$weakest->subject->name}, followed by reassessment next term.";
        }

        return implode(' ', $sentences);
    }
}