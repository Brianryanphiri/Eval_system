<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimetablePeriod extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'name',
        'start_time',
        'end_time',
        'is_break',
        'sort_order',
    ];

    protected $casts = [
        'is_break' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(ClassTimetable::class, 'period_id');
    }

    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeTeaching($query)
    {
        return $query->where('is_break', false);
    }

    public function getTimeLabelAttribute(): string
    {
        $start = substr((string) $this->start_time, 0, 5);
        $end = substr((string) $this->end_time, 0, 5);

        return "{$start} - {$end}";
    }
}