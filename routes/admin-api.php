<?php

use App\Http\Controllers\Admin\Dashboard\BrandController;
use App\Http\Controllers\Admin\Dashboard\CategoryController;
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



// when you modify middleware guard auth:api-admin add it in routeserviceprovider
Route::group(['middleware'=>'auth:api','as'=>'admin.'],function () {

    // Brand Routes
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->middleware('setLang')
            ->name('brands.index');
        
        Route::get('/{id}', [BrandController::class, 'show'])->middleware('setLang')
            ->name('brands.show');
        
        Route::post('/', [BrandController::class, 'store'])
            ->name('brands.store');
        
        // in update problem to us put method 
        // Route::put('/{id}', [BrandController::class, 'update'])
        //     ->name('brands.update');
        Route::post('/{id}', [BrandController::class, 'update'])
            ->name('brands.update');
        
        Route::delete('/{id}', [BrandController::class, 'destroy'])
            ->name('brands.destroy');
    });

    
    // Category Routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->middleware('setLang')
            ->name('categories.index');
        
        Route::get('/{id}', [CategoryController::class, 'show'])->middleware('setLang')
            ->name('categories.show');
        
        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store');
        
        Route::post('/{id}', [CategoryController::class, 'update'])
            ->name('categories.update');
        
        Route::delete('/{id}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');
    });

});