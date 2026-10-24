<?php

namespace App\Services;

use App\Mail\UserInvitedMail;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserInviteService
{
    /**
     * Creates a user with a random temporary password that satisfies the
     * school's own password policy, emails it to them, and flags the
     * account so they're forced to change it on first login.
     *
     * email_verified_at is set immediately: the admin performing this
     * invite is personally vouching for the address, unlike a stranger
     * self-registering a new school, so no separate click-to-verify step
     * is needed here.
     */
    public function invite(School $school, string $name, string $email, string $role, ?string $phone = null): User
    {
        Settings::useSchool($school->id);

        $temporaryPassword = PasswordPolicy::generate();

        $user = User::create([
            'school_id' => $school->id,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'phone' => $phone,
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);

        Mail::to($user->email)->send(new UserInvitedMail($user, $temporaryPassword));

        return $user;
    }
}