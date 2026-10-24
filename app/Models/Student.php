<?php

namespace App\Models;


use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'student_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'photo_path',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_guardian', 'student_id', 'guardian_id')
            ->withPivot('relationship', 'is_primary_contact')
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function subjectTermResults(): HasMany
    {
        return $this->hasMany(SubjectTermResult::class);
    }

    public function termResults(): HasMany
    {
        return $this->hasMany(TermResult::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function currentEnrollment(): ?Enrollment
    {
        return $this->enrollments()
            ->whereHas('academicYear', fn($q) => $q->where('is_current', true))
            ->first();
    }

    public function currentClassroom(): ?Classroom
    {
        return $this->currentEnrollment()?->classroom;
    }

    public function scopeAtRisk($query)
    {
        return $query->whereHas('termResults', function ($q) {
            $q->latest()->limit(1)->where('overall_percentage', '<', 50);
        });
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path
            ? \Storage::disk('public')->url($this->photo_path)
            : null;
    }
}