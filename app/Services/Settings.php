<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Settings
{
    protected static ?int $scopedSchoolId = null;

    public static function useSchool(?int $schoolId): void
    {
        static::$scopedSchoolId = $schoolId;
    }

    public static function forSchool(int $schoolId): static
    {
        static::$scopedSchoolId = $schoolId;

        return new static();
    }

    public static function get(string $key, $default = null)
    {
        $map = static::map();

        if (!array_key_exists($key, $map)) {
            return $default;
        }

        $value = $map[$key];

        return $value === null ? $default : $value;
    }

    public static function set(string $key, $value): void
    {
        if ($value === null) {
            static::forget($key);
            return;
        }

        static::setMany([$key => $value]);
    }

    public static function setMany(array $values): void
    {
        $schoolId = static::schoolId();

        if (!$schoolId || empty($values)) {
            return;
        }

        $lock = Cache::lock("settings:{$schoolId}:write", 10);

        $lock->block(5, function () use ($schoolId, $values) {
            DB::transaction(function () use ($schoolId, $values) {
                foreach ($values as $key => $value) {
                    $type = static::detectType($value);
                    $stored = static::encode($value, $type);

                    Setting::updateOrCreate(
                        ['school_id' => $schoolId, 'key' => $key],
                        ['value' => $stored, 'type' => $type]
                    );
                }
            });

            static::bumpVersion($schoolId);
        });
    }

    public static function forget(string $key): void
    {
        $schoolId = static::schoolId();

        if (!$schoolId) {
            return;
        }

        $lock = Cache::lock("settings:{$schoolId}:write", 10);

        $lock->block(5, function () use ($schoolId, $key) {
            Setting::where('school_id', $schoolId)->where('key', $key)->delete();
            static::bumpVersion($schoolId);
        });
    }

    public static function all(array $defaults): array
    {
        $map = static::map();
        $result = [];

        foreach ($defaults as $key => $default) {
            $value = $map[$key] ?? null;
            $result[$key] = $value === null ? $default : $value;
        }

        return $result;
    }

    public static function flush(): void
    {
        $schoolId = static::schoolId();

        if ($schoolId) {
            static::bumpVersion($schoolId);
        }
    }

    protected static function map(): array
    {
        $schoolId = static::schoolId();

        if (!$schoolId) {
            return [];
        }

        $version = static::version($schoolId);
        $cacheKey = "settings:{$schoolId}:v{$version}:map";

        return Cache::remember($cacheKey, now()->addDay(), function () use ($schoolId) {
            try {
                $rows = Setting::where('school_id', $schoolId)->get(['key', 'value', 'type']);

                $map = [];
                foreach ($rows as $row) {
                    $map[$row->key] = $row->castedValue();
                }

                return $map;
            } catch (\Throwable $e) {
                Log::channel('single')->error('Settings map load failed', [
                    'school_id' => $schoolId,
                    'error' => $e->getMessage(),
                ]);

                return [];
            }
        });
    }

    protected static function version(int $schoolId): int
    {
        $key = "settings:{$schoolId}:version";

        $current = Cache::get($key);

        if ($current === null) {
            $current = 1;
            Cache::forever($key, $current);
        }

        return (int) $current;
    }

    protected static function bumpVersion(int $schoolId): void
    {
        $key = "settings:{$schoolId}:version";

        $current = (int) (Cache::get($key) ?? 1);

        // Purge the map that this version bump is about to orphan.
        Cache::forget("settings:{$schoolId}:v{$current}:map");

        $next = $current + 1;

        Cache::forever($key, $next);
    }

    protected static function detectType($value): string
    {
        return match (true) {
            is_bool($value) => 'bool',
            is_int($value) => 'int',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default => 'string',
        };
    }

    protected static function encode($value, string $type): string
    {
        return match ($type) {
            'bool' => $value ? '1' : '0',
            'int' => (string) (int) $value,
            'float' => (string) (float) $value,
            'json' => json_encode($value),
            default => (string) $value,
        };
    }

    protected static function schoolId(): ?int
    {
        if (static::$scopedSchoolId !== null) {
            return static::$scopedSchoolId;
        }

        return auth()->check() ? auth()->user()->school_id : null;
    }
}