<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        // Identity
        'name',
        'motto',
        'principal_name',
        'principal_signature_path',
        'institution_type',
        'academic_system',
        'country',
        'timezone',
        'currency',
        'registration_number',
        'founded_year',

        // Address
        'address',
        'address_street',
        'address_city',
        'address_province',
        'address_postal_code',

        // Contact
        'phone',
        'phone_secondary',
        'email',
        'website',
        'email_signature',

        // Branding
        'logo_path',
        'favicon_path',
        'primary_color',
        'accent_color',

        // System
        'term_structure',
        'setup_completed',
        'setup_step',
        'public_portal_enabled',
    ];

    protected $casts = [
        'setup_completed' => 'boolean',
        'public_portal_enabled' => 'boolean',
        'founded_year' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function assessmentTypes(): HasMany
    {
        return $this->hasMany(AssessmentType::class);
    }

    public function gradingScales(): HasMany
    {
        return $this->hasMany(GradingScale::class);
    }

    public function promotionRule(): HasMany
    {
        // Typically one active rule, but kept as hasMany in case of historical versions
        return $this->hasMany(PromotionRule::class);
    }

    public function feeCategories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeeCategory::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function currentAcademicYear(): ?AcademicYear
    {
        return $this->academicYears()->where('is_current', true)->first();
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path
            ? \Storage::disk('public')->url($this->logo_path)
            : null;
    }

    public function faviconUrl(): ?string
    {
        return $this->favicon_path
            ? \Storage::disk('public')->url($this->favicon_path)
            : null;
    }

    public function principalSignatureUrl(): ?string
    {
        return $this->principal_signature_path
            ? \Storage::disk('public')->url($this->principal_signature_path)
            : null;
    }

    /**
     * Human-readable full address, falling back to the legacy `address` column.
     */
    public function fullAddress(): ?string
    {
        $parts = array_filter([
            $this->address_street,
            $this->address_city,
            $this->address_province,
            $this->address_postal_code,
            $this->country,
        ]);

        return $parts ? implode(', ', $parts) : $this->address;
    }

    /**
     * One-line contact string for report cards, emails, etc.
     */
    public function contactLine(): string
    {
        return collect([
            $this->phone,
            $this->phone_secondary,
            $this->email,
            $this->website,
        ])->filter()->implode(' · ');
    }
}