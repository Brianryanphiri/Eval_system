<?php

namespace App\Services;

class PeerComparison
{
    private const DEFAULTS = [
        'show_rank' => false,
        'show_percentile' => true,
        'show_class_average' => true,
        'show_subject_average' => true,
        'show_highest_lowest' => false,
        'hide_names' => true,
        'min_class_size' => 5,
    ];

    public static function get(string $key)
    {
        return Settings::get("peer.{$key}", self::DEFAULTS[$key] ?? null);
    }

    public static function showRank(): bool
    {
        return (bool) self::get('show_rank');
    }

    public static function showPercentile(): bool
    {
        return (bool) self::get('show_percentile');
    }

    public static function showClassAverage(): bool
    {
        return (bool) self::get('show_class_average');
    }

    public static function showSubjectAverage(): bool
    {
        return (bool) self::get('show_subject_average');
    }

    public static function showHighestLowest(): bool
    {
        return (bool) self::get('show_highest_lowest');
    }

    public static function hideNames(): bool
    {
        return (bool) self::get('hide_names');
    }

    public static function minClassSize(): int
    {
        return (int) self::get('min_class_size');
    }

    /**
     * Is the class big enough for comparison stats to be shown?
     */
    public static function meetsMinimumSize(int $classSize): bool
    {
        return $classSize >= self::minClassSize();
    }
}