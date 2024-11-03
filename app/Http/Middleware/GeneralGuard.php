<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
class GeneralGuard 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */



    public function handle($request, Closure $next, $guard = null)
    {
        // if ($guard !== null) {
        //     // Set the specified guard
        //     Auth::shouldUse($guard);

        //     // Retrieve the bearer token from the Authorization header
        //     $token = $request->bearerToken();

        //     // Check if a token was provided
        //     if (!$token) {
        //         return response()->json(['error' => 'Unauthorized'], 401);
        //     }

        //     try {
        //         // Authenticate the user using the provided guard
        //         // Sanctum doesn't require the token parsing here; instead, `Auth::guard($guard)->check()` verifies authentication.
        //         if (!Auth::guard($guard)->check() && Auth::user()->role != 'admin') {
        //             return response()->json(['error' => 'Invalid token or unauthenticated user'], 401);
        //         }
        //     } catch (\Exception $e) {
        //         return response()->json(['error' => 'Invalid token'], 401);
        //     }
        // }

        // // If everything is good, proceed with the request
        // return $next($request);



        ##################M2#
        if ($guard !== null) {
            // Set the specified guard
            Auth::shouldUse($guard);
    
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'statusCode' => 401,
                    'message' => 'Unauthorized',
                ],401);  
            }
    
            try{
                // Check if the user is authenticated and has the 'admin' role
                if (Auth::guard($guard)->check() && Auth::user()->role === 'admin') {
                    return $next($request);
                }

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'statusCode' => 401,
                    'message' => 'Invalid token',
                ],401);  
            }
    
            // If the user is not an admin, return unauthorized response
            
            return response()->json([
                'status' => 'error',
                'statusCode' => 403,
                'message' => 'Unauthorized - Admins only',
            ],403);  
        }
    }
        
                    

         
    
}
