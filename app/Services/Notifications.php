<?php

namespace App\Services;

class Notifications
{
    /**
     * Defaults mirror the settings page exactly.
     */
    private const DEFAULTS = [
        'report_card_sent' => true,
        'guardian_update' => true,
        'absence_alert' => false,
        'intervention_reminder' => true,
        'assessment_locked' => true,
        'marking_reminder' => false,
        'weekly_summary' => true,
        'at_risk_digest' => true,
        'new_staff_added' => true,
        'password_changed' => true,
        'login_from_new_device' => false,
    ];

    /**
     * Should this notification be sent? Respects the school's settings.
     */
    public static function enabled(string $key): bool
    {
        $default = self::DEFAULTS[$key] ?? true;

        return (bool) Settings::get("notify.{$key}", $default);
    }

    /**
     * All notification keys with current values.
     */
    public static function all(): array
    {
        return Settings::all(
            collect(self::DEFAULTS)->mapWithKeys(fn($v, $k) => ["notify.{$k}" => $v])->all()
        );
    }
}