<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use BelongsToSchool;
    protected $table = 'attendance_records';

    protected $fillable = [
        'school_id',
        'session_id',
        'student_id',
        'status',
        'minutes_late',
        'reason',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Human label for the status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
            'sick' => 'Sick',
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * Badge color: green, red, amber, blue, grey.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present' => 'green',
            'absent' => 'red',
            'late' => 'amber',
            'excused' => 'blue',
            'sick' => 'amber',
            default => 'grey',
        };
    }
}