<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentNumberSequence extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'year', 'last_number'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}