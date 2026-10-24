<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Question;
use App\Services\TeacherScopeService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create(Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment(auth()->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        // Eager-load relations used by the Blade view for the exam-context bar
        $assessment->load(['subject', 'classroom', 'term']);

        return view('questions.create', compact('assessment'));
    }

    public function store(Request $request, Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment($request->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        $data = $request->validate([
            'status' => 'nullable|in:draft,submitted',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.topic' => 'required|string',
            'questions.*.objective' => 'nullable|string',
            'questions.*.type' => 'required|in:short_answer,essay,mcq,calculation',
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.difficulty' => 'required|in:easy,medium,hard',
            'questions.*.cognitive_level' => 'required|in:remember,understand,apply,analyse,evaluate,create',
        ]);

        $totalAllocated = array_sum(array_column($data['questions'], 'marks'));
        if ($totalAllocated > $assessment->total_marks) {
            return back()->withErrors([
                'questions' => "Allocated marks ({$totalAllocated}) exceed the assessment total ({$assessment->total_marks})."
            ]);
        }

        $assessment->questions()->delete(); // replace wholesale on save, matches the dynamic-form UI

        foreach (array_values($data['questions']) as $i => $q) {
            Question::create([
                'assessment_id' => $assessment->id,
                'question_number' => $i + 1,
                'question_text' => $q['text'],
                'topic' => $q['topic'],
                'learning_objective' => $q['objective'] ?? null,
                'difficulty' => $q['difficulty'],
                'cognitive_level' => $q['cognitive_level'],
                'question_type' => $q['type'],
                'max_marks' => $q['marks'],
            ]);
        }

        $status = $data['status'] ?? 'submitted';
        $assessment->update(['status' => $status]);

        $message = $status === 'draft'
            ? 'Questions saved as draft.'
            : 'Questions saved.';

        return redirect()->route('assessments.show', $assessment)->with('success', $message);
    }
}