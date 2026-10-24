<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSetupCompleted
{
    public function handle(Request $request, Closure $next)
    {
        $school = $request->user()?->school;

        if ($school && !$school->setup_completed) {
            return redirect()->route('setup.step' . $school->setup_step);
        }

        return $next($request);
    }
}