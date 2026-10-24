<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'name', 'code', 'is_compulsory'];

    protected $casts = [
        'is_compulsory' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'class_subject')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    public function assessmentWeights(): HasMany
    {
        return $this->hasMany(AssessmentWeight::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Resolve the effective weighting for this subject: subject-specific override
     * if one exists, otherwise the school-wide default (assessment_weights.subject_id = null).
     */
    public function effectiveWeights(): \Illuminate\Support\Collection
    {
        $override = $this->assessmentWeights()->get();
        if ($override->isNotEmpty()) {
            return $override;
        }

        return AssessmentWeight::where('school_id', $this->school_id)
            ->whereNull('subject_id')
            ->get();
    }
}