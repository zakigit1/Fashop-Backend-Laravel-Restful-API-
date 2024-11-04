<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use imageUploadTrait;

    const FOLDER_PATH = '/uploads/images/';
    const FOLDER_NAME = 'users';


    /**
     * Registers a user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
    */


    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::create($this->getUserData($request));

            if ($request->hasFile('image')) {
                $imageName = $this->uploadImage_Trait($request, 'image', self::FOLDER_PATH, self::FOLDER_NAME);
                $user->update(['image' => $imageName]);
            }

            
            // SEND EMAIL VERICATION 
            $user->sendEmailVerificationNotification();

            DB::commit();
            return $this->success(null,'User registered successfully. Please verify your email.',SUCCESS_STORE_CODE);



            /** if you want to not send email verification  */
            // $token = $user->createToken(User::USER_TOKEN);
            // DB::commit();
            // return response()->json([
            //     'status' => 'success',
            //     'statusCode' => SUCCESS_CODE,
            //     'message' =>'Register successfully.',
            //     'userData' => $user,
            //     'token' => $token->plainTextToken,
            // ],SUCCESS_STORE_CODE);



        } catch (\Exception $ex) {
            DB::rollBack();
  
            return $this->error($ex->getMessage(),ERROR_CODE);
            // return $this->error("something is wrong!",ERROR_CODE);

        }
    }

    private function getUserData(RegisterRequest $request)
    {
        return [
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'username' => strstr($request->email, '@', true),
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'active',//is by default active 
        ];
    }

    /**
     * Logins a user
     *
     * @param LoginRequest $request
     * @return JsonResponse
    */

    public function login(LoginRequest $request){

        try{

            $isValid = $this->isValidCredential($request);// radi tjih array json man function isValidCredential

            if (!$isValid['success']) { //sucess value is false
                return $this->error($isValid['message'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
    
            $user = $isValid['user'];
    
            $user->tokens()->delete();// this new update
    
            // i need to delete the previous token that give it to use when you register
            $token = $user->createToken(User::USER_TOKEN);
    

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
     * Validates user credential
     *
     * @param LoginRequest $request
     * @return array
    */

    private function isValidCredential(LoginRequest $request) : array
    {
        $data = $request->validated();

        // Find User :
        $user = User::where('email', $data['email'])->first();

        // Email Is Invalide :
        if ($user === null) {

            return [
                'success' => false,
                'message' => 'Invalid Credential'
            ];

        }

        // Password Is Valide :
        if (Hash::check($data['password'], $user->password)) {

            return [
                'success' => true,
                'user' => $user
            ];

        }

        // Password Is Invalide :
        return [
            'success' => false,
            'message' => 'Password is not matched',// hadi nrmlemt mndirhch ngolh bli crendtial is not match
        ];

    }


    /**
     * Logins a user with token
     *
     * @return JsonResponse
    */

    public function loginWithToken() : JsonResponse
    {
        return $this->success(auth()->user(),'login successfully!',SUCCESS_CODE);
    }

    /**
     * Logouts a user
     *
     * @param Request $request
     * @return JsonResponse
     */


    ######## this doing deleted just the current token of user :
    public function logout(Request $request) : JsonResponse
    {
        try{
            $request->user()->currentAccessToken()->delete();  //delete the current user authanticated token
            return $this->success(null,'Logged out successfully.',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    ######## this doing deleted all user token  :
    // public function logout(Request $request): JsonResponse
    // {

        // try{
        //     $request->user()->tokens->each(function ($token, $key) {
        //         $token->delete();
        //     });
        //     return $this->success(null,'Logged out successfully.',SUCCESS_DELETE_CODE);
        // }catch(\Exception $ex){ 
        //     return $this->error($ex->getMessage(),ERROR_CODE);
        // }

    // }


}

