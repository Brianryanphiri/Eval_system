<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Student;
use App\Services\TeacherScopeService;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    public function index(Request $request, TeacherScopeService $scope)
    {
        $query = Intervention::where('school_id', $request->user()->school_id)
            ->with('student', 'assignedTeacher');

        $classroomIds = $scope->accessibleClassroomIds($request->user());
        if ($classroomIds !== null) {
            $query->whereHas('student.enrollments', fn($q) => $q->whereIn('class_id', $classroomIds));
        }

        $interventions = $query->latest()->get()
            ->map(fn($iv) => [
                'student' => $iv->student->full_name,
                'class' => $iv->student->currentClassroom()?->name ?? '—',
                'area' => $iv->area,
                'problem' => $iv->problem_description,
                'intervention' => str_replace('_', ' ', ucfirst($iv->intervention_type)),
                'teacher' => $iv->assignedTeacher->name,
                'start' => $iv->start_date->format('d M Y'),
                'review' => $iv->review_date->format('d M Y'),
                'before' => $iv->score_before,
                'target' => $iv->score_target,
                'current' => $iv->score_after ?? $iv->score_before,
                'after' => $iv->score_after,
                'status' => $iv->isOverdue() ? 'overdue' : $iv->status,
            ]);

        return view('interventions.index', compact('interventions'));
    }

    public function create()
    {
        return view('interventions.create');
    }

    public function store(Request $request, TeacherScopeService $scope)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'area' => 'required|string',
            'trigger' => 'nullable|string',
            'problem' => 'required|string',
            'intervention_type' => 'required|string',
            'teacher_id' => 'required|exists:users,id',
            'priority' => 'nullable|in:low,moderate,high',
            'start_date' => 'required|date',
            'review_date' => 'required|date|after:start_date',
            'current_score' => 'required|numeric|min:0|max:100',
            'target_score' => 'required|numeric|min:0|max:100',
        ]);

        $student = Student::findOrFail($data['student_id']);
        abort_unless(
            $scope->canAccessStudent($request->user(), $student),
            403,
            'You do not have access to this student.'
        );

        Intervention::create([
            'school_id' => $request->user()->school_id,
            'student_id' => $data['student_id'],
            'assigned_teacher_id' => $data['teacher_id'],
            'created_by' => $request->user()->id,
            'area' => $data['area'],
            'trigger' => $data['trigger'] ?? 'manual',
            'problem_description' => $data['problem'],
            'intervention_type' => $data['intervention_type'],
            'priority' => $data['priority'] ?? 'moderate',
            'start_date' => $data['start_date'],
            'review_date' => $data['review_date'],
            'score_before' => $data['current_score'],
            'score_target' => $data['target_score'],
            'status' => 'active',
        ]);

        return redirect()->route('interventions.index')->with('success', 'Intervention created.');
    }
}