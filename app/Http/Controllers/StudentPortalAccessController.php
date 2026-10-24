<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\UserInviteService;
use Illuminate\Http\Request;

class StudentPortalAccessController extends Controller
{
    public function store(Request $request, Student $student, UserInviteService $inviteService)
    {
        if ($student->user_id) {
            return back()->withErrors(['email' => 'This student already has a portal account.']);
        }

        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $user = $inviteService->invite(
            $student->school,
            $student->full_name,
            $data['email'],
            'student',
        );

        $student->update(['user_id' => $user->id]);

        return back()->with('success', "Portal access enabled for {$student->full_name}. Login details were emailed.");
    }

    public function destroy(Student $student)
    {
        $student->update(['user_id' => null]);
        // Intentionally does not delete the User record, only unlinks it,
        // preserves login history and avoids orphaning anything referencing that user_id elsewhere.

        return back()->with('success', 'Portal access removed for this student.');
    }
}