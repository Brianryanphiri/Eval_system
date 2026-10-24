<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectModalityMap extends Model
{
    use BelongsToSchool;

    protected $table = 'subject_modality_map';

    protected $fillable = [
        'school_id',
        'subject_id',
        'primary_modality',
        'secondary_modality',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}