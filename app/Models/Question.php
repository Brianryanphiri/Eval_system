<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'assessment_id',
        'question_number',
        'question_text',
        'topic',
        'learning_objective',
        'difficulty',
        'cognitive_level',
        'question_type',
        'max_marks',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function studentMarks(): HasMany
    {
        return $this->hasMany(StudentMark::class);
    }

    /** Success rate = average % scored on this question across all students */
    public function successRate(): float
    {
        $avg = $this->studentMarks()->avg('marks_obtained');
        return $this->max_marks > 0 ? round(($avg / $this->max_marks) * 100, 1) : 0.0;
    }

    public function needsReview(): bool
    {
        return $this->successRate() < 40;
    }
}