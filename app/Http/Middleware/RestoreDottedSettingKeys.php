<?php

namespace App\Http\Middleware;

use App\Settings\SettingsRegistry;
use Closure;
use Illuminate\Http\Request;

class RestoreDottedSettingKeys
{
    /**
     * PHP automatically converts dots (and spaces) in incoming POST/GET
     * field names into underscores before Laravel ever sees the request,
     * because dots are not valid characters in a PHP variable name. This
     * happens at the PHP engine level and cannot be disabled.
     *
     * Every settings form in this app uses field names like
     * "peer.show_rank" or "policy.rounding". By the time the request
     * reaches the controller, PHP has already renamed the submitted key
     * to "peer_show_rank" / "policy_rounding".
     *
     * Laravel's $request->input(...), ->boolean(...), and ->validate([...])
     * all treat a dot in the key as "look inside a nested array" — so they
     * never find these flattened keys, and silently return null/false
     * instead of the value the user actually submitted.
     *
     * This middleware runs before the request reaches the controller. For
     * every known setting key (SettingsRegistry::keys() is the single
     * source of truth for every dotted key used anywhere in Settings),
     * it checks whether the underscore-flattened version was submitted,
     * and if so, merges the value back in under its real dotted key — so
     * every existing $request->input('policy.rounding') style call in the
     * controllers keeps working exactly as written, with no other changes
     * needed anywhere else.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            foreach (SettingsRegistry::keys() as $dottedKey) {
                $flatKey = str_replace('.', '_', $dottedKey);

                if ($request->has($flatKey) && !$request->has($dottedKey)) {
                    $request->merge([$dottedKey => $request->input($flatKey)]);
                }
            }
        }

        return $next($request);
    }
}