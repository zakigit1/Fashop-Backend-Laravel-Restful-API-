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

    const FOLDER_PATH = '/uploads/images/';
    const FOLDER_NAME = 'users';

    public function update_profile(UpdateUserProfileRequest $request): JsonResponse
    {
        try{
            // $user = Auth::user();
            $user = $request->user();
            
            if($user){
                if($user->role == 'admin'){
                    if($request->hasFile('image')){
            
                        $old_image = $user->image;
            
                        // // delete the old image
                        // $this->deleteImage_Trait($old_image,self::FOLDER_PATH,self::FOLDER_NAME);
                        
                        // store the new image in storage folder
                        $imageName= $this->updateImage_Trait($request,'image',self::FOLDER_PATH,self::FOLDER_NAME,$old_image);
            
                        ## Save Image In To DataBase : 
                        $user->image=$imageName;
                    }
            
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->username = $request->username;
                    $user->phone = $request->phone;
                    
                    $user->save();

                    return response()->json([
                        'status' => 'success',
                        'statusCode' => SUCCESS_CODE,
                        'message' =>'Profile updated successfully.',
                        'adminData' => $user,
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


    public function update_profile_password(UpdateUserProfilePasswordRequest $request): JsonResponse
    {

        try{    

            $user = $request->user();
            if($user){
                if($user->role == 'admin'){

                    $user->update([
                        'password'=>Hash::make($request->password),
                        // $user->password = bcrypt($request->password);
                    ]);
        
                    return response()->json([
                        'status' => 'success',
                        'statusCode' => SUCCESS_CODE,
                        'message' =>'Profile Password Updated Successfully !',
                        'adminData' => $user,
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
}