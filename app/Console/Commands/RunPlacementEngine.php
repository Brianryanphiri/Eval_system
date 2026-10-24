<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\PlacementEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunPlacementEngine extends Command
{
    protected $signature = 'placement:run
                            {--school= : School ID to process}
                            {--term= : Term ID (defaults to current term for the school)}';

    protected $description = 'Compute modality profiles and placement recommendations for all students in a school/term.';

    public function handle(PlacementEngine $engine): int
    {
        $schoolId = (int) $this->option('school');
        if ($schoolId <= 0) {
            $this->error('--school is required.');
            return self::FAILURE;
        }

        $termId = $this->option('term');
        if (!$termId) {
            $termId = $this->resolveCurrentTerm($schoolId);
            if (!$termId) {
                $this->error("No current term found for school {$schoolId}.");
                return self::FAILURE;
            }
        }

        $bar = $this->output->createProgressBar();
        $bar->start();

        $counts = ['pending' => 0, 'failed' => 0];

        Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->chunk(200, function ($students) use ($engine, $termId, &$counts, $bar) {
                foreach ($students as $student) {
                    try {
                        $engine->evaluate($student, $termId);
                        $counts['pending']++;
                    } catch (\Throwable $e) {
                        $counts['failed']++;
                        $this->newLine();
                        $this->error("Failed on student {$student->id}: {$e->getMessage()}");
                    }
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Recommendations created: {$counts['pending']}. Failures: {$counts['failed']}.");

        return self::SUCCESS;
    }

    private function resolveCurrentTerm(int $schoolId): ?int
    {
        $year = DB::table('academic_years')
            ->where('school_id', $schoolId)
            ->where('is_current', 1)
            ->first();

        if (!$year) {
            return null;
        }

        return DB::table('terms')
            ->where('academic_year_id', $year->id)
            ->orderBy('start_date')
            ->value('id');
    }
}