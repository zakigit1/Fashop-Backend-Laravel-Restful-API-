<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('lang');
        if (!$locale) {
            $locale = config('app.fallback_locale');
        }

        // Ensure the locale is supported
        if (!in_array($locale, config('app.supported_locales', ['en']))) {
            $locale = config('app.fallback_locale');
        }

        app()->setLocale($locale);
        return $next($request);
    }
}
