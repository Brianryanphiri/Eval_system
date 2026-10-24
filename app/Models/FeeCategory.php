<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeCategory extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'name', 'is_compulsory'];

    protected $casts = ['is_compulsory' => 'boolean'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function structures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }
}