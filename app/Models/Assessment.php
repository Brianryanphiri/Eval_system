<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'term_id',
        'class_id',
        'subject_id',
        'assessment_type_id',
        'created_by',
        'name',
        'category',
        'total_marks',
        'duration_minutes',
        'pass_mark',
        'contribution_percentage',
        'status',
        'locked_at',
    ];

    protected $casts = ['locked_at' => 'datetime'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function assessmentType(): BelongsTo
    {
        return $this->belongsTo(AssessmentType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('question_number');
    }

    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    public function allocatedMarks(): int
    {
        return $this->questions()->sum('max_marks');
    }
}