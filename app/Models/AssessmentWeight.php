<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentWeight extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'assessment_type_id', 'subject_id', 'weight_percentage'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function assessmentType(): BelongsTo
    {
        return $this->belongsTo(AssessmentType::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}