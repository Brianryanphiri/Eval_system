<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Overview: list all classes with student counts.
     */
    public function overview()
    {
        $schoolId = auth()->user()->school_id;

        $classes = Classroom::where('school_id', $schoolId)
            ->withCount('students')
            ->orderBy('name')
            ->get();

        return view('academic.classes.overview', compact('classes'));
    }

    public function create()
    {
        $teachers = User::where('school_id', auth()->user()->school_id)
            ->where('role', 'teacher')
            ->get();

        return view('academic.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        Classroom::create([
            'school_id' => $request->user()->school_id,
            ...$data,
        ]);

        return redirect()->route('academic.index')->with('success', 'Class created.');
    }

    public function edit(Classroom $class)
    {
        $teachers = User::where('school_id', $class->school_id)
            ->where('role', 'teacher')
            ->get();

        return view('academic.classes.edit', [
            'classroom' => $class,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request, Classroom $class)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        $class->update($data);

        return redirect()->route('academic.index')->with('success', 'Class updated.');
    }

    public function destroy(Classroom $class)
    {
        $class->delete();

        return redirect()->route('academic.index')->with('success', 'Class removed.');
    }
}