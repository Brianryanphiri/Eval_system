<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentModalityProfile extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'term_id',
        'audio_score',
        'visual_score',
        'practical_score',
        'top_modality',
        'is_ambiguous',
        'data_points',
        'computed_at',
    ];

    protected $casts = [
        'audio_score' => 'float',
        'visual_score' => 'float',
        'practical_score' => 'float',
        'is_ambiguous' => 'boolean',
        'computed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}