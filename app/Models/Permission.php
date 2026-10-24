<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['key', 'label', 'group'];

    /**
     * Permissions are global (not scoped to a school), so this model does
     * NOT use the BelongsToSchool trait.
     */
}