<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlacementRecommendation;
use App\Models\Student;
use App\Services\PlacementEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function __construct(private PlacementEngine $engine)
    {
    }

    public function evaluate(string $studentId, Request $request): JsonResponse
    {
        $request->validate(['term_id' => 'required|integer|exists:terms,id']);
        $student = Student::findOrFail($studentId);
        $placement = $this->engine->evaluate($student, (int) $request->input('term_id'));

        return response()->json($this->present($student, $placement));
    }

    public function list(Request $request): JsonResponse
    {
        $request->validate(['term_id' => 'required|integer|exists:terms,id']);

        $rows = PlacementRecommendation::with('student')
            ->where('term_id', $request->integer('term_id'))
            ->when(
                $request->filled('school_id'),
                fn($q) => $q->where('school_id', $request->integer('school_id'))
            )
            ->latest('id')
            ->limit(500)
            ->get();

        return response()->json($rows->map(fn($r) => [
            'placement_id' => $r->id,
            'student_id' => $r->student_id,
            'name' => $r->student?->full_name,
            'current_class' => $r->current_class,
            'recommended_class' => $r->recommended_class,
            'reason_codes' => $r->reason_codes,
            'corrective_actions' => $r->corrective_actions,
            'status' => $r->status,
        ]));
    }

    public function apply(string $placementId, Request $request): JsonResponse
    {
        $placement = PlacementRecommendation::findOrFail($placementId);

        if (!in_array('RECLASSIFY', $placement->corrective_actions, true)) {
            return response()->json([
                'status' => 'not_applicable',
                'actions' => $placement->corrective_actions,
            ], 422);
        }

        // NOTE: For a first version, "apply" just marks the recommendation as applied.
        // Moving the student between classes is a separate action that touches
        // enrollments and the class hierarchy — do it manually or in a follow-up
        // iteration once the school has agreed on the class structure.
        $placement->update([
            'status' => 'applied',
            'applied_by' => $request->user()?->id,
            'applied_at' => now(),
        ]);

        return response()->json(['status' => 'applied', 'placement_id' => $placement->id]);
    }

    private function present(Student $student, PlacementRecommendation $p): array
    {
        $profile = \App\Models\StudentModalityProfile::where('student_id', $student->id)
            ->where('term_id', $p->term_id)
            ->first();

        return [
            'student_id' => $student->id,
            'name' => $student->full_name,
            'current_class' => $p->current_class,
            'modality_profile' => $profile ? [
                'audio' => $profile->audio_score,
                'visual' => $profile->visual_score,
                'practical' => $profile->practical_score,
            ] : null,
            'recommended_class' => $p->recommended_class,
            'reason_codes' => $p->reason_codes,
            'corrective_actions' => $p->corrective_actions,
        ];
    }
}