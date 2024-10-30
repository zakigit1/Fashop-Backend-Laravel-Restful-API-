<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */



     
    // public function handle(Request $request, Closure $next, string ...$guards): Response
    // {

    //     $guards = empty($guards) ? [null] : $guards;

    //     ########## M1 : 
    //     // foreach ($guards as $guard) {//guard : auth
    //     //     if (Auth::guard($guard)->check()) {
    //     //         if($request->user()->role == 'admin'){
    //     //             return redirect(RouteServiceProvider::ADMIN);
    //     //         }else{
    //     //             return redirect(RouteServiceProvider::HOME);
    //     //         }
    //     //     }
    //     // }

    //     // return $next($request);
        
        
    //     ########## M2 : if you add guard use this method 

    //     foreach ($guards as $guard) {// guest
    //         if (Auth::guard($guard)->check()) {
    //             if ($guard == 'admin-api'){
    //                 return redirect(RouteServiceProvider::ADMIN);
    //             }
    //             else{// $guard == 'Web'
    //                 return redirect(RouteServiceProvider::HOME);
    //             } 
    //         }  
    //     }

    //     return $next($request);

    // }




    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {

            if($guard == 'admin-api')
                return redirect(RouteServiceProvider::ADMIN);
            else
                return redirect(RouteServiceProvider::HOME);

        }

        return $next($request);
    }
}
