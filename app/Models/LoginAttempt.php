<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = ['email', 'ip_address', 'user_agent', 'successful'];

    protected $casts = [
        'successful' => 'boolean',
    ];
}