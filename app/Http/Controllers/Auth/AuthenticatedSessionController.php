<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\Settings;
use App\Services\SessionPolicy;
use App\Services\TwoFactorAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $email = $credentials['email'];

        // Lockout policy is per-school. If this email matches an account,
        // scope Settings to that school so its real policy applies. If it
        // doesn't match anything, Settings::get() below simply falls back
        // to the registry defaults on its own -- so the rest of this
        // method behaves identically either way. That's deliberate: it's
        // what stops the lockout/attempts-remaining messaging from
        // revealing whether an email is actually registered.
        $targetUser = User::where('email', $email)->first();

        if ($targetUser && $targetUser->school_id) {
            Settings::useSchool($targetUser->school_id);
        }

        $lockoutEnabled = Settings::get('security.lockout_after_attempts', true);
        $maxAttempts = (int) Settings::get('security.lockout_attempts', 5);
        $lockoutMinutes = (int) Settings::get('security.lockout_minutes', 15);

        if ($lockoutEnabled) {
            $recentFailures = LoginAttempt::where('email', $email)
                ->where('successful', false)
                ->where('created_at', '>=', now()->subMinutes($lockoutMinutes))
                ->count();

            // Deliberately not logging an attempt row here: while locked,
            // repeated submissions must not extend the lockout window or
            // spam the table. The window is driven only by genuine
            // attempts made before the lock triggered.
            if ($recentFailures >= $maxAttempts) {
                throw ValidationException::withMessages([
                    'email' => "Too many failed login attempts. Please try again in {$lockoutMinutes} minutes.",
                ]);
            }
        }

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            LoginAttempt::create([
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'successful' => false,
            ]);

            $message = 'These credentials do not match our records.';

            if ($lockoutEnabled) {
                $failuresNow = $recentFailures + 1;
                $remaining = max(0, $maxAttempts - $failuresNow);

                if ($remaining > 0) {
                    $message .= ' ' . $remaining . ' attempt' . ($remaining === 1 ? '' : 's')
                        . ' remaining before temporary lockout.';
                } else {
                    $message = "Too many failed login attempts. Please try again in {$lockoutMinutes} minutes.";
                }
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        // Password is correct. Log it as a successful credential check for
        // lockout-tracking purposes regardless of what happens with 2FA
        // next -- lockout is about password guessing, not code guessing.
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => true,
        ]);

        $authenticatedUser = Auth::user();

        if (TwoFactorAuthenticator::isRequiredFor($authenticatedUser)) {
            // Undo the login Auth::attempt() just performed -- the user is
            // not considered fully signed in until they pass the code
            // challenge. This keeps using the same guest session, so the
            // "intended URL" set by the auth middleware still survives
            // the round trip through the verification page.
            Auth::logout();

            TwoFactorAuthenticator::issueChallenge($authenticatedUser, $remember);

            return redirect()->route('two-factor.verify');
        }

        $request->session()->regenerate();

        SessionPolicy::establish($authenticatedUser, $request);

        $authenticatedUser->update(['last_login_at' => now()]);

        AuditLogger::log(
            'login',
            User::class,
            $authenticatedUser->id,
            "User logged in ({$authenticatedUser->email})"
        );

        return redirect()->intended($this->redirectPathForRole($authenticatedUser->role));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            AuditLogger::log(
                'logout',
                \App\Models\User::class,
                $user->id,
                "User logged out ({$user->email})"
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /** Where each role lands immediately after login -- matches the portal built for their role */
    private function redirectPathForRole(string $role): string
    {
        return match ($role) {
            'teacher' => route('teacher.dashboard'),
            'parent' => route('parent.dashboard'),
            'student' => route('student.dashboard'),
            default => route('dashboard'), // super_admin, school_admin, academic_director, head_of_department
        };
    }
}