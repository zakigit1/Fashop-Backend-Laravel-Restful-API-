<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
                'status' => 'success',
                'statusCode' => SUCCESS_CODE,
                'message' =>'Logged in successfully.',
                'userData' => $user,
                'token' => $token->plainTextToken,
            ],SUCCESS_CODE);
            
        }catch (ValidationException $ex) {
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }  
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        try{
            $request->user()->currentAccessToken()->delete();
            return $this->success(null,'Logged out successfully.',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
