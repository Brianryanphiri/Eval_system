<?php

namespace App\Services;

use Illuminate\Validation\Rules\Password;

class PasswordPolicy
{
    private const DEFAULTS = [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_number' => true,
        'require_symbol' => false,
        'expiry_days' => 0,
        'prevent_reuse_count' => 3,
    ];

    public static function get(string $key)
    {
        return Settings::get("password.{$key}", self::DEFAULTS[$key] ?? null);
    }

    /**
     * Returns a Laravel Password rule object built from the school's policy.
     * Use this in any $request->validate([...]) call:
     *
     *   'password' => ['required', 'confirmed', PasswordPolicy::rule()],
     */
    public static function rule(): Password
    {
        $rule = Password::min((int) self::get('min_length'));

        if (self::get('require_uppercase')) {
            $rule->mixedCase();  // Laravel's mixedCase requires both upper and lower
        }
        if (self::get('require_number')) {
            $rule->numbers();
        }
        if (self::get('require_symbol')) {
            $rule->symbols();
        }

        return $rule;
    }

    /**
     * Generates a random password that satisfies this school's own policy
     * -- used for admin-issued temporary passwords (see UserInviteService),
     * so a generated password is never rejected by the same rules it's
     * meant to comply with. Ambiguous-looking characters (0/O, 1/l/I) are
     * excluded so a person typing the password from an email doesn't
     * mistake one character for another.
     */
    public static function generate(): string
    {
        $minLength = max((int) self::get('min_length'), 8);

        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '!@#$%^&*-_=+';

        $required = [];
        if (self::get('require_uppercase')) {
            $required[] = $upper[random_int(0, strlen($upper) - 1)];
        }
        if (self::get('require_lowercase')) {
            $required[] = $lower[random_int(0, strlen($lower) - 1)];
        }
        if (self::get('require_number')) {
            $required[] = $numbers[random_int(0, strlen($numbers) - 1)];
        }
        if (self::get('require_symbol')) {
            $required[] = $symbols[random_int(0, strlen($symbols) - 1)];
        }

        $pool = $upper . $lower . $numbers . (self::get('require_symbol') ? $symbols : '');

        $remaining = max($minLength - count($required), 0);
        $rest = [];
        for ($i = 0; $i < $remaining; $i++) {
            $rest[] = $pool[random_int(0, strlen($pool) - 1)];
        }

        $chars = array_merge($required, $rest);
        shuffle($chars);

        return implode('', $chars);
    }

    public static function expiryDays(): int
    {
        return (int) self::get('expiry_days');
    }

    public static function preventReuseCount(): int
    {
        return (int) self::get('prevent_reuse_count');
    }
}