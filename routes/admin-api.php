<?php

use App\Http\Controllers\Admin\Dashboard\BrandController;
use App\Http\Controllers\Admin\Dashboard\CategoryController;
use App\Http\Controllers\Admin\Dashboard\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|                   API Routes (-*- JUST FOR ADMIN -*-)
|--------------------------------------------------------------------------
|
*/



Route::group(['middleware'=>['admin-api:api'],'as'=>'admin.'],function () {

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
        Route::post('/{id}/update', [BrandController::class, 'update'])
            ->name('brands.update');
        
        Route::delete('/{id}/delete', [BrandController::class, 'destroy'])
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
        
        // Route::put('/{id}', [CategoryController::class, 'update'])
        //     ->name('categories.update');
        Route::post('/{id}/update', [CategoryController::class, 'update'])
            ->name('categories.update');
        
        Route::delete('/{id}/delete', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');
    });


    // Product Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->middleware('setLang')
            ->name('products.index');
        
        Route::get('/{id}', [ProductController::class, 'show'])->middleware('setLang')
            ->name('products.show');
        
        Route::post('/', [ProductController::class, 'store'])
            ->name('products.store');
        
        // Route::put('/{id}/update', [ProductController::class, 'update'])
        //     ->name('products.update');
        Route::post('/{id}/update', [ProductController::class, 'update'])
            ->name('products.update');
        
        Route::delete('/{id}/delete', [ProductController::class, 'destroy'])
            ->name('products.destroy');
    });

});