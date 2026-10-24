<?php

namespace App\Services;

use App\Models\FinanceNumberSequence;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class FinanceNumberGenerator
{
    public function nextInvoiceNumber(School $school): string
    {
        return $this->next($school, 'invoice', 'INV');
    }

    public function nextReceiptNumber(School $school): string
    {
        return $this->next($school, 'receipt', 'RCT');
    }

    private function next(School $school, string $type, string $prefix): string
    {
        return DB::transaction(function () use ($school, $type, $prefix) {
            $year = now()->year;

            $sequence = FinanceNumberSequence::where('school_id', $school->id)
                ->where('year', $year)
                ->where('type', $type)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                FinanceNumberSequence::create([
                    'school_id' => $school->id,
                    'year' => $year,
                    'type' => $type,
                    'last_number' => 0,
                ]);

                $sequence = FinanceNumberSequence::where('school_id', $school->id)
                    ->where('year', $year)
                    ->where('type', $type)
                    ->lockForUpdate()
                    ->first();
            }

            $nextNumber = $sequence->last_number + 1;
            $sequence->update(['last_number' => $nextNumber]);

            return sprintf('%s-%d-%05d', $prefix, $year, $nextNumber);
        });
    }
}