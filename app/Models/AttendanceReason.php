<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceReason extends Model
{
    use BelongsToSchool;
    protected $table = 'attendance_reasons';

    protected $fillable = [
        'school_id',
        'label',
        'requires_note',
        'is_active',
    ];

    protected $casts = [
        'requires_note' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Only active reasons, for this school.
     */
    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('label');
    }
}