<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\SessionPolicy;
use App\Services\TwoFactorAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        $pending = $request->session()->get('two_factor');

        if (!$pending) {
            return redirect()->route('login');
        }

        $user = User::find($pending['user_id']);

        return view('auth.two-factor-verify', [
            'email' => $user->email ?? null,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => 'required|string',
        ]);

        $pending = $request->session()->get('two_factor');

        if (!$pending) {
            return redirect()->route('login')
                ->withErrors(['code' => 'Your session has expired. Please log in again.']);
        }

        if (now()->greaterThan($pending['expires_at'])) {
            $request->session()->forget('two_factor');

            return redirect()->route('login')
                ->withErrors(['email' => 'That code has expired. Please log in again.']);
        }

        if (!Hash::check($data['code'], $pending['code_hash'])) {
            throw ValidationException::withMessages([
                'code' => 'That code is incorrect.',
            ]);
        }

        $user = User::findOrFail($pending['user_id']);

        Auth::login($user, $pending['remember'] ?? false);
        $request->session()->forget('two_factor');
        $request->session()->regenerate();

        SessionPolicy::establish($user, $request);

        $user->update(['last_login_at' => now()]);

        AuditLogger::log(
            'login',
            User::class,
            $user->id,
            "User logged in ({$user->email})"
        );

        return redirect()->intended($this->redirectPathForRole($user->role));
    }

    public function resend(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('two_factor');

        if (!$pending) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($pending['user_id']);

        TwoFactorAuthenticator::issueChallenge($user, $pending['remember'] ?? false);

        return back()->with('info', 'A new code has been sent to your email.');
    }

    private function redirectPathForRole(string $role): string
    {
        return match ($role) {
            'teacher' => route('teacher.dashboard'),
            'parent' => route('parent.dashboard'),
            'student' => route('student.dashboard'),
            default => route('dashboard'),
        };
    }
}