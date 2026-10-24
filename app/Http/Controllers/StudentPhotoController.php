<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentPhotoController extends Controller
{
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($student->photo_path) {
            Storage::disk('public')->delete($student->photo_path);
        }

        $path = $request->file('photo')->store("students/{$student->school_id}", 'public');

        $student->update(['photo_path' => $path]);

        return back()->with('success', "{$student->full_name}'s photo updated.");
    }

    public function destroy(Student $student)
    {
        if ($student->photo_path) {
            Storage::disk('public')->delete($student->photo_path);
            $student->update(['photo_path' => null]);
        }

        return back()->with('success', 'Photo removed.');
    }
}