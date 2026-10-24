<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Existing forced-change flag
        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        // Password expiry check
        $expiryDays = \App\Services\PasswordPolicy::expiryDays();
        if ($expiryDays > 0) {
            $lastChanged = $user->password_changed_at ?? $user->created_at;
            if ($lastChanged && $lastChanged->diffInDays(now()) >= $expiryDays) {
                return redirect()->route('password.change')
                    ->with('warning', "Your password has expired. Please set a new one.");
            }
        }

        return $next($request);
    }
}