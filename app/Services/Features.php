<?php

namespace App\Services;

class Features
{
    private const DEFAULTS = [
        'interventions' => true,
        'analytics' => true,
        'report_cards' => true,
        'guardian_portal' => true,
        'student_portal' => true,
        'public_portal' => false,
        'sms_notifications' => false,
        'ai_insights' => false,
        'bulk_import' => true,
        'api_access' => false,
    ];

    public static function enabled(string $key): bool
    {
        $default = self::DEFAULTS[$key] ?? true;

        return (bool) Settings::get("features.{$key}", $default);
    }
}