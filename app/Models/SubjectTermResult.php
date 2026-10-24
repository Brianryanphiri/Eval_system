<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTermResult extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'term_id',
        'final_percentage',
        'grade_letter',
        'achievement_level',
        'passed',
        'previous_term_percentage',
        'change_from_previous',
        'calculated_at',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'calculated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function trendDirection(): string
    {
        return ($this->change_from_previous ?? 0) >= 0 ? 'up' : 'down';
    }
}