<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\Subject;
use App\Services\TeacherScopeService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request, TeacherScopeService $scope)
    {
        $query = Assessment::where('school_id', $request->user()->school_id)
            ->with('subject', 'classroom', 'term');

        $classroomIds = $scope->accessibleClassroomIds($request->user());
        if ($classroomIds !== null) {
            $query->whereIn('class_id', $classroomIds);
        }

        $assessments = $query->latest()->paginate(20);

        return view('assessments.index', compact('assessments'));
    }

    public function create(Request $request, TeacherScopeService $scope)
    {
        $school = $request->user()->school;

        $classroomIds = $scope->accessibleClassroomIds($request->user());
        $classrooms = Classroom::where('school_id', $school->id)
            ->when($classroomIds !== null, fn($q) => $q->whereIn('id', $classroomIds))
            ->get();

        return view('assessments.create', [
            'classrooms' => $classrooms,
            'subjects' => Subject::where('school_id', $school->id)->get(),
            'assessmentTypes' => AssessmentType::where('school_id', $school->id)->where('is_active', true)->get(),
            'currentTerm' => $school->currentAcademicYear()?->currentTerm(),
        ]);
    }

    public function store(Request $request, TeacherScopeService $scope)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string',
            'term' => 'required|exists:terms,id',
            'assessment_type' => 'required|exists:assessment_types,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'total_marks' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'pass_mark' => 'nullable|integer|min:0|max:100',
            'contribution' => 'required|numeric|min:0|max:100',
        ]);

        abort_unless(
            $scope->canAccessClassroom($request->user(), (int) $data['class_id']),
            403,
            'You cannot create assessments for this class.'
        );

        $assessment = Assessment::create([
            'school_id' => $request->user()->school_id,
            'term_id' => $data['term'],
            'class_id' => $data['class_id'],
            'subject_id' => $data['subject_id'],
            'assessment_type_id' => $data['assessment_type'],
            'created_by' => $request->user()->id,
            'name' => $data['name'],
            'category' => 'summative',
            'total_marks' => $data['total_marks'],
            'duration_minutes' => $data['duration_minutes'],
            'pass_mark' => $data['pass_mark'] ?? null,
            'contribution_percentage' => $data['contribution'],
            'status' => 'draft',
        ]);

        return redirect()->route('questions.create', $assessment)
            ->with('success', 'Assessment created. Now add your questions.');
    }

    public function show(Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment(auth()->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        $assessment->load('questions', 'subject', 'classroom');
        return view('assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment(auth()->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        if ($assessment->isLocked()) {
            abort(403, 'Locked assessments cannot be edited.');
        }

        return view('assessments.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment($request->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        if ($assessment->isLocked()) {
            abort(403, 'Locked assessments cannot be edited.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'pass_mark' => 'nullable|integer|min:0|max:100',
            'contribution' => 'required|numeric|min:0|max:100',
        ]);

        $assessment->update([
            'name' => $data['name'],
            'total_marks' => $data['total_marks'],
            'duration_minutes' => $data['duration_minutes'],
            'pass_mark' => $data['pass_mark'] ?? null,
            'contribution_percentage' => $data['contribution'],
        ]);

        return redirect()->route('assessments.show', $assessment)->with('success', 'Assessment updated.');
    }

    public function destroy(Assessment $assessment, TeacherScopeService $scope)
    {
        abort_unless(
            $scope->canAccessAssessment(auth()->user(), $assessment),
            403,
            'You do not have access to this assessment.'
        );

        if ($assessment->isLocked()) {
            abort(403, 'Locked assessments cannot be deleted.');
        }

        $assessment->delete();

        return redirect()->route('assessments.index')->with('success', 'Assessment deleted.');
    }
}