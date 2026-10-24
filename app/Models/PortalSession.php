<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSession extends Model
{
    protected $fillable = ['user_id', 'session_id', 'device', 'ip_address', 'last_seen_at', 'revoked_at'];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}