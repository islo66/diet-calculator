<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['ro']);
        $fallback = config('app.fallback_locale', 'ro');
        $locale = $fallback;

        $user = Auth::user();
        if ($user && $user->locale && in_array($user->locale, $supported, true)) {
            $locale = $user->locale;
        } elseif ($request->routeIs('home')) {
            foreach ($request->getLanguages() as $lang) {
                $lang = strtolower(str_replace('_', '-', $lang));
                $short = substr($lang, 0, 2);
                if (in_array($short, $supported, true)) {
                    $locale = $short;
                    break;
                }
            }
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
