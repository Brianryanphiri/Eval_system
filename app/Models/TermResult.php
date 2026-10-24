<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermResult extends Model
{
    protected $fillable = [
        'student_id',
        'term_id',
        'class_id',
        'overall_percentage',
        'achievement_level',
        'position',
        'class_size',
        'attendance_percentage',
        'promotion_status',
        'promotion_reason',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReportComment::class);
    }

    public function isAtRisk(): bool
    {
        return $this->overall_percentage < 50;
    }
}