<?php

namespace App\Http\Middleware;

use App\Services\Features;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    /**
     * Usage in routes:
     *   Route::middleware('feature:interventions')->group(...);
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (!Features::enabled($feature)) {
            abort(404, 'This feature is not enabled for your school.');
        }

        return $next($request);
    }
}