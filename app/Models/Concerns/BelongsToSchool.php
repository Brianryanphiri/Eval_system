<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes every query on a model to the current user's school_id.
 *
 * Models using this trait must have a `school_id` column. The scope is
 * applied automatically on all reads. Writes still need to set school_id
 * explicitly, or call setSchoolIdFromAuth() before saving.
 *
 * Super admins bypass the scope so they can work across schools.
 *
 * To intentionally query across schools, call ->withoutSchoolScope() on
 * the query.
 */
trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope('school', function (Builder $query) {
            $user = auth()->user();

            // No logged-in user, or the model has no school_id column,
            // or the user is a super admin: skip the scope.
            if (!$user || !$user->school_id) {
                return;
            }

            if ($user->role === 'super_admin') {
                return;
            }

            $query->where(
                $query->getModel()->qualifyColumn('school_id'),
                $user->school_id
            );
        });

        // On create, fill school_id from the auth user if not provided.
        static::creating(function ($model) {
            if (!$model->school_id) {
                $user = auth()->user();
                if ($user && $user->school_id) {
                    $model->school_id = $user->school_id;
                }
            }
        });
    }

    /**
     * Run a query without the automatic school scope. Use sparingly.
     */
    public function scopeWithoutSchoolScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('school');
    }
}