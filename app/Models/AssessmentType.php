<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentType extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'key', 'name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function weights(): HasMany
    {
        return $this->hasMany(AssessmentWeight::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * The school-wide default weight for this type (subject_id = null).
     */
    public function defaultWeight(): ?float
    {
        $weight = $this->weights
            ->whereNull('subject_id')
            ->first();

        return $weight?->weight_percentage;
    }
}