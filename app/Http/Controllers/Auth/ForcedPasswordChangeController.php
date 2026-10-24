<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordPolicy;
use App\Services\Settings;
use App\Services\SessionPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForcedPasswordChangeController extends Controller
{
    public function edit()
    {
        return view('auth.force-password-change');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', PasswordPolicy::rule()],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        if (Settings::get('session.force_logout_on_password', true)) {
            SessionPolicy::revokeAllExceptCurrent($user, $request->session()->getId());
        }

        return redirect()->route('dashboard')->with('success', 'Password updated. Welcome back.');
    }
}