<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingScale extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'letter', 'min_score', 'max_score', 'interpretation'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Resolve a raw percentage to its grade band for a given school.
     */
    public static function resolve(int $schoolId, float $percentage): ?self
    {
        return static::where('school_id', $schoolId)
            ->where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->first();
    }

    /** Scope to a school, ordered top-band-first */
    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId)
            ->orderByDesc('min_score');
    }
}