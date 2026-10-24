<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'student_id',
        'term_id',
        'invoice_number',
        'total_amount',
        'amount_paid',
        'status',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function balance(): float
    {
        return round($this->total_amount - $this->amount_paid, 2);
    }

    public function isFullyPaid(): bool
    {
        return $this->balance() <= 0;
    }

    /** Recalculates amount_paid and status from actual payment rows, call after any payment change */
    public function recalculate(): void
    {
        $paid = $this->payments()->sum('amount');

        $status = match (true) {
            $paid <= 0 => 'unpaid',
            $paid < $this->total_amount => 'partial',
            default => 'paid',
        };

        $this->update(['amount_paid' => $paid, 'status' => $status]);
    }
}