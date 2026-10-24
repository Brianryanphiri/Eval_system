<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\Setting;
use App\Settings\SettingsRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillSettings extends Command
{
    protected $signature = 'settings:backfill {--school= : Only backfill a specific school id}';

    protected $description = 'Insert any missing setting rows for every school from the registry.';

    public function handle(): int
    {
        $query = School::query();

        if ($this->option('school')) {
            $query->where('id', (int) $this->option('school'));
        }

        $schools = $query->get();

        if ($schools->isEmpty()) {
            $this->warn('No schools found.');
            return self::SUCCESS;
        }

        $fields = SettingsRegistry::fields();
        $inserted = 0;
        $skipped = 0;

        foreach ($schools as $school) {
            $existing = Setting::where('school_id', $school->id)
                ->pluck('key')
                ->all();

            $existingLookup = array_flip($existing);

            $rows = [];
            $now = now();

            foreach ($fields as $key => $definition) {
                if (isset($existingLookup[$key])) {
                    $skipped++;
                    continue;
                }

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
                DB::table('settings')->insert($chunk);
                $inserted += count($chunk);
            }
        }

        $this->info("Inserted {$inserted} settings. Skipped {$skipped} existing rows.");
        return self::SUCCESS;
    }
}