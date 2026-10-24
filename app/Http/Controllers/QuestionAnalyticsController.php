<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Services\TeacherScopeService;
use App\Services\TopicMasteryAnalyzer;
use Illuminate\Http\Request;

class QuestionAnalyticsController extends Controller
{
    public function show(Request $request, Assessment $assessment, TopicMasteryAnalyzer $analyzer, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment($request->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        $assessment->load('questions.studentMarks', 'subject', 'classroom');

        $questions = $assessment->questions->map(fn($q) => [
            'no' => $q->question_number,
            'topic' => $q->topic,
            'objective' => $q->learning_objective ?? '—',
            'success' => $q->successRate(),
            'difficulty' => $q->difficulty,
        ])->all();

        $studentCount = $assessment->classroom->currentStudents()->count();
        $classAverage = $assessment->results()->avg('percentage');

        $topics = $analyzer->topicMasteryForClassroom($assessment->classroom, $assessment->term)
            ->map(fn($score, $topic) => [$topic, $score])
            ->values()->all();

        return view('analytics.question-performance', [
            'assessment' => $assessment,
            'questions' => $questions,
            'topics' => $topics,
            'studentCount' => $studentCount,
            'classAverage' => $classAverage ? round($classAverage, 1) : 0,
        ]);
    }
}