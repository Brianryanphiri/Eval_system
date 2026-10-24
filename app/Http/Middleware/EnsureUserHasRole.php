<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    /**
     * Usage in routes: ->middleware('role:school_admin,super_admin')
     * Comma-separated roles, any one of them is enough to pass.
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $userRole = $request->user()?->role;

        if (!$userRole || !in_array($userRole, $roles, true)) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}