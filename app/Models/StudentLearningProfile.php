<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLearningProfile extends Model
{
    use BelongsToSchool;
    protected $table = 'student_learning_profiles';

    protected $fillable = [
        'school_id',
        'student_id',
        'term_id',
        'overall_score',
        'peer_average',
        'z_score',
        'spread',
        'volatility',
        'overall_delta',
        'flag',
        'subject_deltas',
        'computed_at',
    ];

    protected $casts = [
        'subject_deltas' => 'array',
        'computed_at' => 'datetime',
        'overall_score' => 'float',
        'peer_average' => 'float',
        'z_score' => 'float',
        'spread' => 'float',
        'volatility' => 'float',
        'overall_delta' => 'float',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Human-readable label for the flag.
     */
    public function getFlagLabelAttribute(): string
    {
        return match ($this->flag) {
            'at_risk' => 'At Risk',
            'needs_support' => 'Needs Support',
            'watch' => 'Watch',
            'uneven' => 'Uneven Profile',
            'improving' => 'Improving',
            'high_achiever' => 'High Achiever',
            'on_track' => 'On Track',
            default => ucfirst(str_replace('_', ' ', (string) $this->flag)),
        };
    }

    /**
     * Badge color hint: red, amber, green.
     */
    public function getFlagColorAttribute(): string
    {
        return match ($this->flag) {
            'at_risk' => 'red',
            'needs_support' => 'amber',
            'watch' => 'amber',
            'uneven' => 'amber',
            'improving' => 'green',
            'high_achiever' => 'green',
            'on_track' => 'green',
            default => 'grey',
        };
    }
}