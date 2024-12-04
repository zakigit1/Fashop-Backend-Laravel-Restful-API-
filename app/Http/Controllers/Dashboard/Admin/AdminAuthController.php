<?php

namespace App\Http\Controllers\Dashboard\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Authenticate admin user and generate token
     */
    public function store(LoginRequest $request): JsonResponse
    { 
        try {
            if (!$this->attemptLogin($request)) {
                return $this->error('Invalid credentials', UNAUTHORIZED);
            }

            $user = $request->user();
            
            if (!$this->isAdmin($user)) {
                return $this->error('You are not authorized to access the admin panel', FORBIDDEN);
            }

            return $this->generateAuthResponse($user);
        } catch (ValidationException $ex) {
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) { 
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    /**
     * Logout admin user and revoke token
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->error('Unauthorized', UNAUTHORIZED);
            }

            if (!$this->isAdmin($user)) {
                return $this->error('You are not authorized to access the admin panel', FORBIDDEN);
            }

            $this->revokeToken($user);
            return $this->success(null, 'Logged out successfully.', SUCCESS_DELETE_CODE);
        } catch (\Exception $ex) { 
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    /**
     * Attempt to authenticate the user
     */
    private function attemptLogin(LoginRequest $request): bool
    {
        $credentials = $request->only('email', 'password');
        return Auth::attempt($credentials);
    }

    /**
     * Check if user is an admin
     */
    private function isAdmin($user): bool
    {
        return $user && $user->role === 'admin';
    }

    /**
     * Generate authentication response with token
     */
    private function generateAuthResponse($user): JsonResponse
    {
        $user->tokens()->delete(); // Revoke all existing tokens
        $token = $user->createToken('api-token-login');

        return response()->json([
            'status' => 'success',
            'statusCode' => SUCCESS_CODE,
            'message' => 'Logged in successfully.',
            'adminData' => $user,
            'token' => $token->plainTextToken,
        ], SUCCESS_CODE);
    }

    /**
     * Revoke the user's current token
     */
    private function revokeToken($user): void
    {
        $user->currentAccessToken()->delete();
    }
}
