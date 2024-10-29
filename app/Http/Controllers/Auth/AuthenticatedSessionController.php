<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        try{

            $request->authenticate();// this is like Auth::attemp 
    
            $user = $request->user();
        
            if($user->role == 'user'){
                if (!$user->hasVerifiedEmail()) {
                    Auth::logout();
                    return response()->json(['message' => 'Your email address is not verified. Please check your email.'], 403);
                }
            }
    
            $user->tokens()->delete();
        
            $token = $user->createToken('api-token-login');
        
               
            return response()->json([
                'userData' => $user,
                'token' => $token->plainTextToken,
            ],200);
            
        }catch(\Exception $ex){
            
            return response()->json(['error' => $ex->getMessage()], 500);

        }
        

        
        // return response()->json(['error' => 'Unauthorized'], 401);
        

    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ],200);
    }
}
