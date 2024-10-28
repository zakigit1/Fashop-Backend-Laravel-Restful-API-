<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\imageUploadTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{

    use imageUploadTrait;

    const FOLDER_PATH = '/uploads/images/';
    const FOLDER_NAME = 'users';



    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:6','max:20'],
        ]);

        $user_image = null;
        if($request->hasFile('image')){
            if ($request->hasFile('image')) {
                $user_image = $this->uploadImage_Trait($request, 'image', self::FOLDER_PATH, self::FOLDER_NAME);
            }
        }

        $user = User::create([
            'image' => $user_image,
            'name' => $request->name,
            'email' => $request->email,
            'username' => strstr($request->email, '@', true),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        event(new Registered($user));


        $user->sendEmailVerificationNotification();


        return response()->json(['message' => 'User registered successfully. Please verify your email.'], 201);




        /**if you dont use email verification use this code */

        // Auth::login($user);

        // $token = $user->createToken('api-token-register');


        // return response()->json([
        //     'userData' => $user,
        //     'token' => $token->plainTextToken,
        // ],200);


        // return response()->noContent();
    }
}
