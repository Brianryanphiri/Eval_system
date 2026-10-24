<?php

namespace App\Http\Controllers;

use App\Mail\GuardianProgressUpdate;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StudentGuardianEmailController extends Controller
{
    public function send(Request $request, Student $student)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        if (!\App\Services\Notifications::enabled('guardian_update')) {
            return back()->withErrors([
                'guardian' => 'Guardian progress emails are disabled for this school.',
            ]);
        }

        $data = $request->validate([
            'guardian_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:2000',
        ]);

        $guardian = $student->guardians()->where('users.id', $data['guardian_id'])->firstOrFail();

        Mail::to($guardian->email)->queue(
            new GuardianProgressUpdate($student, $data['message'] ?? null)
        );

        $student->guardians()->updateExistingPivot($guardian->id, [
            'last_contacted_at' => now(),
        ]);

        return back()->with('success', "Progress update sent to {$guardian->name}.");
    }
}