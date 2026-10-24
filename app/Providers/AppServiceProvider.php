<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\School;
use App\Observers\SchoolObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        School::observe(SchoolObserver::class);

        $this->registerPermissionGates();
    }

    /**
     * Register one Gate per permission key in the permissions table.
     * A user passes a gate if their role is granted that permission in
     * role_permission for their own school. Super admins pass everything.
     *
     * Gates are registered once per request. New permissions added at
     * runtime need php artisan optimize:clear (or config:clear) to be
     * picked up on the next request.
     */
    private function registerPermissionGates(): void
    {
        // Super admins bypass every check without a database query.
        Gate::before(function ($user, string $ability) {
            if ($user && $user->role === 'super_admin') {
                return true;
            }
            return null;
        });

        // If no database connection is available (e.g. during composer
        // install or the very first boot), skip gate registration so the
        // app can still start. Anything that asks for a permission will
        // fall through to the default deny.
        try {
            $permissions = Permission::query()->get(['id', 'key']);
        } catch (\Throwable $e) {
            return;
        }

        foreach ($permissions as $permission) {
            Gate::define($permission->key, function ($user) use ($permission) {
                if (!$user->school_id) {
                    return false;
                }

                return DB::table('role_permission')
                    ->where('school_id', $user->school_id)
                    ->where('role', $user->role)
                    ->where('permission_id', $permission->id)
                    ->exists();
            });
        }
    }
}