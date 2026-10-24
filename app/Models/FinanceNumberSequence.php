<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceNumberSequence extends Model
{
    protected $fillable = ['school_id', 'year', 'type', 'last_number'];
}