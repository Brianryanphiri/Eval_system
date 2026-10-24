<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $parent = $request->user();
        $children = $parent->children()->with('termResults', 'subjectTermResults.subject')->get();

        if ($children->isEmpty()) {
            abort(404, 'No student records are linked to this account yet.');
        }

        // Support the ?child=ID switcher from the child-tabs UI; default to the first child
        $selected = $children->firstWhere('id', $request->get('child')) ?? $children->first();

        $currentTerm = $parent->school->currentAcademicYear()?->currentTerm();
        $termResult = $selected->termResults->where('term_id', $currentTerm?->id)->first();

        $subjects = $selected->subjectTermResults
            ->where('term_id', $currentTerm?->id)
            ->map(fn($r) => [$r->subject->name, $r->final_percentage, $r->trendDirection()])
            ->values()->all();

        return view('parent.dashboard', [
            'children' => $children,
            'selectedChild' => $selected,
            'subjects' => $subjects,
            'strengths' => collect($subjects)->where('1', '>=', 65)->pluck('0')->all(),
            'support' => collect($subjects)->where('1', '<', 50)->pluck('0')->all(),
        ]);
    }
}