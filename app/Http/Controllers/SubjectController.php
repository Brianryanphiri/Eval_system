<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function create()
    {
        return view('academic.subjects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'is_compulsory' => 'boolean',
        ]);

        Subject::create([
            'school_id' => $request->user()->school_id,
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'is_compulsory' => $data['is_compulsory'] ?? false,
        ]);

        return redirect()->route('academic.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject)
    {
        return view('academic.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'is_compulsory' => 'boolean',
        ]);

        $subject->update($data);

        return redirect()->route('academic.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('academic.index')->with('success', 'Subject removed.');
    }
}