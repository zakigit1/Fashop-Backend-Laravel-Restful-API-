<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\User\AuthController;
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

Route::middleware(['auth:sanctum','checkPassword'])->get('/user', function (Request $request) {
    return $request->user();
});

// this for testing api 
Route::post('/api-online',function(){
    dd('We Are TN DEVELOPER Team!');
});


Route::group(['prefix'=>'user','middleware'=>'checkPassword'],function(){


    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);


    Route::group(['middleware'=>'auth:sanctum'],function(){
        Route::post('/loginWithToken',[AuthController::class,'loginWithToken']);
        Route::post('/logout',[AuthController::class,'logout']);
    });

});






// test of redirect middleware : 

// Route::get('admin/dashboard',function(){
//     dd('You Are in Admin Dashboard');
// })->middleware('auth');//after add auth:admin

// Route::get('/dashboard',function(){
//     dd('You Are in User Dashboard');
// })->middleware('auth');
