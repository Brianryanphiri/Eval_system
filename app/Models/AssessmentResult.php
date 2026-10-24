<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentResult extends Model
{
    protected $fillable = [
        'student_id',
        'assessment_id',
        'total_obtained',
        'percentage',
        'grade_letter',
        'passed',
        'last_modified_by',
    ];

    protected $casts = ['passed' => 'boolean'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(ResultAuditLog::class);
    }
}