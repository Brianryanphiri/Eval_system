<?php

namespace App\Http\Middleware;

use App\Models\PortalSession;
use App\Services\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceSessionPolicy
{
    private const REVOCATION_CHECK_TTL_SECONDS = 30;

    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $lifetimeMinutes = (int) Settings::get('session.lifetime_minutes', 120);
        $sessionId = $request->session()->getId();

        $lastActivity = $request->session()->get('pts_last_activity');

        if ($lastActivity && now()->diffInMinutes($lastActivity) > $lifetimeMinutes) {
            return $this->forceLogout($request, 'Your session expired due to inactivity. Please log in again.');
        }

        $request->session()->put('pts_last_activity', now()->toDateTimeString());

        $checkedAt = $request->session()->get('pts_revocation_checked_at');
        $stillFresh = $checkedAt && now()->diffInSeconds($checkedAt) < self::REVOCATION_CHECK_TTL_SECONDS;

        if ($stillFresh) {
            return $next($request);
        }

        $portalSession = PortalSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->first();

        if ($portalSession && $portalSession->revoked_at) {
            return $this->forceLogout(
                $request,
                'You have been signed out because your account was signed in elsewhere, or your password was changed.'
            );
        }

        if ($portalSession) {
            $portalSession->update(['last_seen_at' => now()]);
        }

        $request->session()->put('pts_revocation_checked_at', now()->toDateTimeString());

        return $next($request);
    }

    private function forceLogout(Request $request, string $message)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('warning', $message);
    }
}