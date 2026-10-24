<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicStructureController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $classes = Classroom::where('school_id', $schoolId)
            ->with('subjects', 'homeroomTeacher')
            ->get()
            ->map(fn($c) => [
                'name' => $c->name,
                'meta' => ($c->homeroomTeacher ? "Homeroom: {$c->homeroomTeacher->name}" : 'No homeroom teacher assigned'),
                'students' => $c->currentStudents()->count(),
                'subjects' => $c->subjects->count(),
                'avg' => 0, // requires aggregating subject_term_results, wire up once results data exists
            ]);

        $subjects = Subject::where('school_id', $schoolId)
            ->with('assessmentWeights')
            ->get()
            ->map(fn($s) => [
                'name' => $s->name,
                'code' => $s->code ?? '—',
                'icon' => 'fa-book',
                'weights' => $s->effectiveWeights()->map(fn($w) => $w->assessmentType->name . ' ' . $w->weight_percentage . '%')->all(),
                'compulsory' => $s->is_compulsory,
            ]);

        return view('academic.classes-subjects', compact('classes', 'subjects'));
    }
}