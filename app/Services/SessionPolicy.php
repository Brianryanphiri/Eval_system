<?php

namespace App\Services;

use App\Models\PortalSession;
use App\Models\User;
use Illuminate\Http\Request;

class SessionPolicy
{
    /**
     * Called right after a login completes (session already regenerated,
     * so we record the FINAL session id, not the pre-login one). If
     * single_device_only is on for this user's school, every other
     * active session for this account is revoked first.
     */
    public static function establish(User $user, Request $request): void
    {
        $sessionId = $request->session()->getId();

        if (Settings::get('session.single_device_only', false)) {
            PortalSession::where('user_id', $user->id)
                ->where('session_id', '!=', $sessionId)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);
        }

        PortalSession::updateOrCreate(
            ['user_id' => $user->id, 'session_id' => $sessionId],
            [
                'device' => substr((string) $request->userAgent(), 0, 64),
                'ip_address' => $request->ip(),
                'last_seen_at' => now(),
                'revoked_at' => null,
            ]
        );
    }

    /**
     * Revokes every OTHER tracked session for this user, keeping the
     * current one active -- used when a password change should sign the
     * account out everywhere else, per session.force_logout_on_password.
     */
    public static function revokeAllExceptCurrent(User $user, string $currentSessionId): void
    {
        PortalSession::where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }
}