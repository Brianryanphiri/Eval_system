<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('settings.school-profile', ['school' => $request->user()->school]);
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        $school = $request->user()->school;

        // Delete the old logo first so we don't accumulate orphaned files
        if ($school->logo_path) {
            Storage::disk('public')->delete($school->logo_path);
        }

        $path = $request->file('logo')->store('logos', 'public');

        $school->update(['logo_path' => $path]);

        return back()->with('success', 'School logo updated.');
    }
}