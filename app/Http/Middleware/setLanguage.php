<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class setLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $locale = $request->header('Lang');
        if (!$locale || !in_array($locale, config('translatable.locales'))) {
            $locale = config('translatable.fallback_locale');
        }
        app()->setLocale($locale);
        return $next($request);
        

    }
}
