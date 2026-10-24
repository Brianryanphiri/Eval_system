<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TwoFactorAuthenticator
{
    public const CODE_LIFETIME_MINUTES = 10;

    /**
     * Whether this specific user must complete an email-code challenge
     * before their login is considered complete. Requires 2fa.available
     * AND 2fa.method_email both being on — if email codes are disabled
     * school-wide, "required" settings are ignored rather than locking
     * everyone out with no way to receive a code.
     */
    public static function isRequiredFor(User $user): bool
    {
        if (!Settings::get('2fa.available', true) || !Settings::get('2fa.method_email', true)) {
            return false;
        }

        if (Settings::get('2fa.required_all', false)) {
            return true;
        }

        if (Settings::get('2fa.required_admins', false) && in_array($user->role, ['school_admin', 'super_admin'], true)) {
            return true;
        }

        if (Settings::get('2fa.required_teachers', false) && $user->role === 'teacher') {
            return true;
        }

        return false;
    }

    /**
     * Generates a fresh code, stores its hash (not the plaintext) in the
     * session alongside who it's for and the original "remember me" choice,
     * and emails it. Shared by both the initial login challenge and the
     * "resend code" action so there's one source of truth for the format.
     */
    public static function issueChallenge(User $user, bool $remember): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session()->put('two_factor', [
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_LIFETIME_MINUTES),
            'remember' => $remember,
        ]);

        Mail::to($user->email)->send(new TwoFactorCodeMail($code));
    }
}