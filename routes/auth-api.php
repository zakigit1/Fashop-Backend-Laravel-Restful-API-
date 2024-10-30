<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



// Route::group([], function () {

    /************************************** ADMIN  AUTH Start **********************************************************/

        Route::group(['prefix'=>'admin','as'=>'admin.',],function(){

            Route::post('/login', [AdminAuthController::class, 'store'])
                        // ->middleware(['guest'])
                        ->middleware(['guest:api'])
                        ->name('login');

            Route::post('/logout', [AdminAuthController::class, 'destroy'])
            ->middleware('admin-api:api')
            ->name('logout');
        });


    /************************************** ADMIN AUTH End *************************************************************/





    /************************************** APP AUTH Start *************************************************************/

        Route::post('/register', [RegisteredUserController::class, 'store'])
                        ->middleware('guest')
                        ->name('register');

        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                        ->middleware(['guest','verified'])
                        ->name('login');

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                        ->middleware('guest')
                        ->name('password.email');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
                        ->middleware('guest')
                        ->name('password.store');


        // Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        //                 ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
        //                 ->name('verification.verify');

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                        ->middleware(['auth:sanctum', 'throttle:6,1'])
                        ->name('verification.send');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                        ->middleware('auth:sanctum')
                        ->name('logout');
                
        
        // for email verfication
        Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            // ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
    /************************************** APP AUTH End ***************************************************************/

// });