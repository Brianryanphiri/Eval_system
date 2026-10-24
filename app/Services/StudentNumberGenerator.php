<?php

namespace App\Services;

use App\Models\School;
use App\Models\StudentNumberSequence;
use Illuminate\Support\Facades\DB;

class StudentNumberGenerator
{
    /**
     * Generates the next student number for a school, e.g. "ST-2026-00125".
     * Uses a row lock on the sequence table so concurrent requests can't
     * both read the same "next number" and collide.
     */
    public function next(School $school): string
    {
        return DB::transaction(function () use ($school) {
            $year = now()->year;

            // lockForUpdate() blocks any other transaction from reading this row
            // until this one commits — that's what makes concurrent calls safe.
            $sequence = StudentNumberSequence::where('school_id', $school->id)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = StudentNumberSequence::create([
                    'school_id' => $school->id,
                    'year' => $year,
                    'last_number' => 0,
                ]);

                // Re-fetch with a lock now that it exists, to stay consistent
                // with the locked-read path above under concurrent first-time inserts.
                $sequence = StudentNumberSequence::where('school_id', $school->id)
                    ->where('year', $year)
                    ->lockForUpdate()
                    ->first();
            }

            $nextNumber = $sequence->last_number + 1;
            $sequence->update(['last_number' => $nextNumber]);

            return sprintf('ST-%d-%05d', $year, $nextNumber);
        });
    }
}