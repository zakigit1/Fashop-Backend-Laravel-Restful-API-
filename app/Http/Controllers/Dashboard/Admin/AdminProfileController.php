<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfilePasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminProfileController extends Controller
{
    use imageUploadTrait;

    private const FOLDER_PATH = '/uploads/images/';
    private const FOLDER_NAME = 'users';

    public function update_profile(UpdateUserProfileRequest $request)
    {
        
        try {

            $user = $request->user();

            $error = $this->checkAuthenticatedAdmin($user);

            
            if($error){
                return $error;
              
            }
            
        
            if ($request->hasFile('image')) {
                $user->image = $this->updateProfileImage($request, $user->image);
            }

            $this->updateUserDetails($user, $request);
            
            return $this->generateProfileResponse($user, 'Profile updated successfully.');
     

        } catch (ValidationException $ex) {
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function update_profile_password(UpdateUserProfilePasswordRequest $request)
    {
        try {
            $user = $request->user();

            $error = $this->checkAuthenticatedAdmin($user);

            if($error){
                return $error;
            }
            
            $this->updatePassword($user, $request->password);
            
            return $this->generateProfileResponse($user, 'Profile Password Updated Successfully!');

        } catch (ValidationException $ex) {
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    /**
     * Get and validate authenticated admin user
     */
    private function checkAuthenticatedAdmin($user): bool|JsonResponse     
    {

        if (!$user) {
            // throw new \Exception('Unauthorized', UNAUTHORIZED);
            return $this->error('Unauthorized', UNAUTHORIZED);
        }
        
        if ($user->role !== 'admin') {
            // throw new \Exception('You are not authorized to access the admin panel', FORBIDDEN);
          return   $this->error('You are not authorized to access the admin panel', FORBIDDEN);
        }

        if($user->id == 1){
            // throw new \Exception('You do not have permission to modify the super admin account', UNAUTHORIZED);
          return  $this->error('You do not have permission to modify the super admin account', UNAUTHORIZED);
        }
        
        return false;
    }

    /**
     * Update user profile image
     */
    private function updateProfileImage(Request $request, ?string $oldImage): string
    {
        return $this->updateImage_Trait(
            $request,
            'image',
            self::FOLDER_PATH,
            self::FOLDER_NAME,
            $oldImage
        );
    }

    /**
     * Update user profile details
     */
    private function updateUserDetails($user, UpdateUserProfileRequest $request): void
    {
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
        ])->save();
    }

    /**
     * Update user password
     */
    private function updatePassword($user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    /**
     * Generate profile update response
     */
    private function generateProfileResponse($user, string $message): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'statusCode' => SUCCESS_CODE,
            'message' => $message,
            'adminData' => $user,
        ], SUCCESS_CODE);
    }
}
