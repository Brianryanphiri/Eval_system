<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\UserInviteService;
use Illuminate\Http\Request;

class StudentGuardianController extends Controller
{
    public function store(Request $request, Student $student, UserInviteService $inviteService)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'relationship' => 'nullable|string|max:50',
            'is_primary_contact' => 'boolean',
        ]);

        $guardian = $inviteService->invite(
            $student->school,
            $data['name'],
            $data['email'],
            'parent',
        );

        $student->guardians()->attach($guardian->id, [
            'relationship' => $data['relationship'] ?? null,
            'is_primary_contact' => $data['is_primary_contact'] ?? false,
        ]);

        return back()->with('success', "{$data['name']} has been invited as a guardian. Login details were emailed to them.");
    }

    public function destroy(Student $student, \App\Models\User $guardian)
    {
        $student->guardians()->detach($guardian->id);

        return back()->with('success', 'Guardian removed from this student.');
    }
}