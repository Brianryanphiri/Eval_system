<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use BelongsToSchool;

    public const TRACKS = ['audio', 'visual', 'practical', 'mixed'];

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'grade_level',
        'learning_track',
        'homeroom_teacher_id',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'class_id', 'student_id')
            ->withPivot(['academic_year_id', 'status'])
            ->withTimestamps();
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function currentStudents()
    {
        return Student::whereHas('enrollments', function ($q) {
            $q->where('class_id', $this->id)->where('status', 'active');
        });
    }

    public function scopeInGrade(Builder $query, ?string $grade): Builder
    {
        return $grade === null
            ? $query->whereNull('grade_level')
            : $query->where('grade_level', $grade);
    }

    public function scopeOnTrack(Builder $query, string $track): Builder
    {
        return $query->where('learning_track', $track);
    }

    public function getTrackLabelAttribute(): string
    {
        return match ($this->learning_track) {
            'audio' => 'Audio learners',
            'visual' => 'Visual learners',
            'practical' => 'Practical learners',
            'mixed' => 'Mixed',
            default => 'Not assigned',
        };
    }
}