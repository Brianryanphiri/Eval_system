<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use BelongsToSchool;
    protected $fillable = ['school_id', 'key', 'value', 'type'];

    protected $casts = [
        'value' => 'string',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function castedValue()
    {
        return match ($this->type) {
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'int' => (int) $this->value,
            'float' => (float) $this->value,
            'json' => $this->decodeJson(),
            'null' => null,
            default => $this->value,
        };
    }

    public function getCastedValueAttribute()
    {
        return $this->castedValue();
    }

    private function decodeJson()
    {
        if ($this->value === null || $this->value === '') {
            return null;
        }

        $decoded = json_decode($this->value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}