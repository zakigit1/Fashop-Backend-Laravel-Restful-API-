<?php

namespace App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     *  authenticated . [Login]
     */
    public function store(LoginRequest $request): JsonResponse
    { 
        try{
            $credentials = $request->only('email', 'password');
            // dd($credentials);
    
            if (Auth::attempt($credentials)) {
                $user = $request->user();
                // dd($user);
    
                if($user->role == 'admin'){
                    
                    $user->tokens()->delete();
            
                    $token = $user->createToken('api-token-login');
            
                    return response()->json([
                        'status' => 'success',
                        'statusCode' => SUCCESS_CODE,
                        'message' =>'Logged in successfully.',
                        'adminData' => $user,
                        'token' => $token->plainTextToken,
                    ],SUCCESS_CODE);
    
                }else{
                    return $this->error('You are not allowed to enter here',FORBIDDEN);
                }
            }else{
                return $this->error('Unauthorized',UNAUTHORIZED);
            }
        }catch (ValidationException $ex) {
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }


    /**
     * Destroy an authenticated . [Logout]
     */
    public function destroy(Request $request): JsonResponse
    {
        // Sanctum handeling token automatically
        try{
            $user = $request->user();
            if(!$user){
                if($user->role == 'admin'){
                    $user->currentAccessToken()->delete();
                    return $this->success(null,'Logged out successfully.',SUCCESS_DELETE_CODE);
                }else{
                    return $this->error('You are not allowed to enter here',FORBIDDEN);
                }    
            }else{
                return $this->error('Unauthorized',UNAUTHORIZED);
            }

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
