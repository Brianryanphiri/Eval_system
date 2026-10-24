<?php

namespace App\Http\Middleware;

use App\Services\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->shouldForce($request)) {
            return $next($request);
        }

        if ($request->secure()) {
            return $next($request);
        }

        $url = 'https://' . $request->getHost()
            . ($request->getPort() && !in_array($request->getPort(), [80, 443], true) ? ':' . $request->getPort() : '')
            . $request->getRequestUri();

        return redirect()->away($url, 301);
    }

    private function shouldForce(Request $request): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user->school_id === null) {
            return false;
        }

        return (bool) Settings::forSchool($user->school_id)->get('security.force_https', false);
    }
}