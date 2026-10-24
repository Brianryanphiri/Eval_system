<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightingTemplateItem extends Model
{
    protected $fillable = [
        'weighting_template_id',
        'assessment_type_id',
        'weight_percentage',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WeightingTemplate::class, 'weighting_template_id');
    }

    public function assessmentType(): BelongsTo
    {
        return $this->belongsTo(AssessmentType::class);
    }
}