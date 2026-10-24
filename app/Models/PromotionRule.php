<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromotionRule extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'rule_type', 'min_overall_average', 'max_failed_subjects'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(PromotionRuleCondition::class);
    }
}