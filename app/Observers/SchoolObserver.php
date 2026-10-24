<?php

namespace App\Observers;

use App\Models\School;
use App\Models\Setting;
use App\Settings\SettingsRegistry;

class SchoolObserver
{
    public function created(School $school): void
    {
        $fields = SettingsRegistry::fields();
        $rows = [];
        $now = now();

        foreach ($fields as $key => $definition) {
            $type = $definition['type'] ?? 'string';
            $default = $definition['default'] ?? null;

            $value = match ($type) {
                'bool' => $default ? '1' : '0',
                'int' => (string) (int) $default,
                'float' => (string) (float) $default,
                'json' => json_encode($default),
                default => (string) $default,
            };

            $rows[] = [
                'school_id' => $school->id,
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            Setting::insert($chunk);
        }
    }
}