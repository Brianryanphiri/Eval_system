<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intervention extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'student_id',
        'subject_id',
        'assigned_teacher_id',
        'created_by',
        'area',
        'trigger',
        'problem_description',
        'intervention_type',
        'priority',
        'start_date',
        'review_date',
        'score_before',
        'score_target',
        'score_after',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'review_date' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function assignedTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_teacher_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function improvementPercentage(): ?float
    {
        return $this->score_after !== null
            ? round($this->score_after - $this->score_before, 2)
            : null;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->review_date->isPast();
    }
}