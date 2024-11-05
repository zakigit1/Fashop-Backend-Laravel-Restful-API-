<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// ? auth:sunctum or auth:api  the same .


Route::middleware(['auth:sanctum','checkPassword'])->get('/user', function (Request $request) {
    return $request->user();
});

// this for testing api 
Route::post('/api-online',function(){
    dd('We Are TN DEVELOPER Team!');
});


// Route::group(['prefix'=>'user','middleware'=>'checkPassword'],function(){
Route::group(['prefix'=>'user'],function(){

    // Auth routes :
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);

    // Profile routes :

    Route::post('profile/update', [ProfileController::class, 'update_profile'])->name('profile.update');
    Route::post('profile/update/password', [ProfileController::class, 'update_profile_password'])->name('profile.update.password');


    Route::group(['middleware'=>'auth:sanctum'],function(){
        Route::post('/loginWithToken',[AuthController::class,'loginWithToken']);
        Route::post('/logout',[AuthController::class,'logout']);
    });

    /** ----------------------------------------------------------------------------------------------------------------------- */




});






// test of redirect middleware : 

// Route::get('admin/dashboard',function(){
//     dd('You Are in Admin Dashboard');
// })->middleware('auth');//after add auth:admin

// Route::get('/dashboard',function(){
//     dd('You Are in User Dashboard');
// })->middleware('auth');
