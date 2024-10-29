<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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

        $locale = $request->header('lang');
        // $locale = $request->lang;


        // Ensure the locale is supported
        if (!$locale || !array_key_exists($locale, config('translatable.locales.'.config('translatable.locale')))){

            $locale = config('translatable.fallback_locale');
            // $locale = config('app.fallback_locale');
        }


        ######### M 1 :
        Config::set('translatable.locale',$locale);
        // Config::set('app.locale',$locale);
        
        ######### M 2 :
        // config(['translatable.locale' => $locale]);
        // app()->setLocale($locale);

        return $next($request);
        
    }
}


