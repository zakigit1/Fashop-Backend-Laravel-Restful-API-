<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    

    public function store(LoginRequest $request): JsonResponse
    {
        
        $credentials = $request->only('email', 'password');
        
        // dd($credentials);

        if (Auth::attempt($credentials)) {
            
            $user = $request->user();
            
            // dd($user);

            if($user->role == 'admin'){
                
                $user->tokens()->delete();
        
                $token = $user->createToken('api-token-login');
        
                return response()->json([
                    'adminData' => $user,
                    'token' => $token->plainTextToken,
                ],200);

            }else{
                return response()->json(['error' => 'You are not allowed to enter here'], 401);
            }

        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {

        // dd($request->user());

        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ],200);
    }







}
