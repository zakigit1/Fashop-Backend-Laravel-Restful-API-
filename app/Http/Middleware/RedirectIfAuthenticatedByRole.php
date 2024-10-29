<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
            // Check if the user is authenticated
            if (Auth::check()) {
                $user = Auth::user();
    
                // Redirect based on role
                if ($user->role === 'admin') {
                    return redirect(RouteServiceProvider::ADMIN);
                }else{
                    return redirect(RouteServiceProvider::HOME);
                }
            }
    
            return $next($request);
        }
        

}
