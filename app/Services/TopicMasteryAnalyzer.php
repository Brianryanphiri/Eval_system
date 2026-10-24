<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Question;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Collection;

class TopicMasteryAnalyzer
{
    /**
     * Aggregates success rate by topic across all locked assessments for a
     * given classroom + term — powers both the Teacher Dashboard's Topic
     * Performance bars and the Question Analytics page's Topic Mastery panel.
     */
    public function topicMasteryForClassroom(Classroom $classroom, Term $term): Collection
    {
        $questions = Question::whereHas('assessment', function ($q) use ($classroom, $term) {
            $q->where('class_id', $classroom->id)
                ->where('term_id', $term->id)
                ->where('status', 'locked');
        })
            ->whereNotNull('topic')
            ->with('studentMarks')
            ->get();

        return $questions
            ->groupBy('topic')
            ->map(function (Collection $topicQuestions) {
                $totalPossible = 0;
                $totalObtained = 0;

                foreach ($topicQuestions as $question) {
                    $totalPossible += $question->max_marks * $question->studentMarks->count();
                    $totalObtained += $question->studentMarks->sum('marks_obtained');
                }

                return $totalPossible > 0 ? round(($totalObtained / $totalPossible) * 100, 1) : 0.0;
            })
            ->sortDesc();
    }

    /**
     * Builds the "Instructional Insight" recommendation for the teacher
     * dashboard — picks the weakest topic below a mastery threshold and
     * generates the same style of recommendation shown in your doc's Section 19.
     */
    public function lowestMasteryInsight(Collection $topicMastery, float $classAverage): ?array
    {
        if ($topicMastery->isEmpty()) {
            return null;
        }

        $weakestTopic = $topicMastery->keys()->last();
        $weakestScore = $topicMastery->last();

        // Only surface an insight if the gap is meaningful — otherwise every
        // dashboard load would flag something even when performance is fine
        if ($weakestScore >= ($classAverage - 10)) {
            return null;
        }

        return [
            'topic' => $weakestTopic,
            'mastery' => $weakestScore,
            'gap_note' => "Well below the {$classAverage}% class average across other topics.",
            'recommendation' => "Review {$weakestTopic} concepts and provide additional application-based exercises. Consider a short diagnostic quiz before reteaching to confirm the specific gap.",
        ];
    }
}