<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeightingTemplate extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'name',
        'description',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WeightingTemplateItem::class);
    }

    /**
     * The total weight across all items — should equal 100 for a valid template.
     */
    public function totalWeight(): float
    {
        return (float) $this->items->sum('weight_percentage');
    }

    /**
     * Whether the template's items sum to exactly 100%.
     */
    public function isComplete(): bool
    {
        return abs($this->totalWeight() - 100.0) < 0.01;
    }
}