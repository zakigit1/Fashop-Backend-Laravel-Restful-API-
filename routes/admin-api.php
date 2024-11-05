<?php

use App\Http\Controllers\Admin\Dashboard\AdminProfileController;
use App\Http\Controllers\Admin\Dashboard\BrandController;
use App\Http\Controllers\Admin\Dashboard\CategoryController;
use App\Http\Controllers\Admin\Dashboard\Product\ProductAttributeController;
use App\Http\Controllers\Admin\Dashboard\Product\ProductAttributeValueController;
use App\Http\Controllers\Admin\Dashboard\Product\ProductController;
use App\Http\Controllers\Admin\Dashboard\Product\ProductGalleryController;
use App\Http\Controllers\Admin\Dashboard\Product\ProductTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|                   API Routes (-*- JUST FOR ADMIN -*-)
|--------------------------------------------------------------------------
|
*/



Route::group(['middleware'=>['admin-api:api'],],function () {

    //Admin Profile :
    Route::prefix('profile')->group(function () {
        Route::post('/update', [AdminProfileController::class, 'update_profile']);
        Route::post('/update/password', [AdminProfileController::class, 'update_profile_password']);
    });
      
    // Brand Routes :
    Route::prefix('brands')->group(function () {
        // Route::get('/', [BrandController::class, 'index'])->middleware('setLang');
        Route::get('/', [BrandController::class, 'index']);
        Route::get('/{id}', [BrandController::class, 'show'])->middleware('setLang');
        Route::post('/', [BrandController::class, 'store']);

        // in update problem to us put method 
        // Route::put('/{id}', [BrandController::class, 'update']);
        Route::post('/{id}/update', [BrandController::class, 'update']);
        
        Route::delete('/{id}/delete', [BrandController::class, 'destroy']);
    });

    
    // Category Routes :
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->middleware('setLang');
        Route::get('/{id}', [CategoryController::class, 'show'])->middleware('setLang');
        Route::post('/', [CategoryController::class, 'store']);
        
        // Route::put('/{id}', [CategoryController::class, 'update']);
        Route::post('/{id}/update', [CategoryController::class, 'update']);
        
        Route::delete('/{id}/delete', [CategoryController::class, 'destroy']);
    });


    // Product Routes :
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->middleware('setLang');
        Route::get('/{id}', [ProductController::class, 'show'])->middleware('setLang');
        Route::post('/', [ProductController::class, 'store']);
        
        // Route::put('/{id}/update', [ProductController::class, 'update']);
        Route::post('/{id}/update', [ProductController::class, 'update']);
        
        Route::delete('/{id}/delete', [ProductController::class, 'destroy']);
    });


    // Product Gallery Routes :
    Route::prefix('product-image-gallery')->group(function () {
        Route::get('',[ProductGalleryController::class,'index']);
        Route::post('',[ProductGalleryController::class,'store']);
        Route::DELETE('{id}/destroy',[ProductGalleryController::class,'destroy']);
        // Route::DELETE('{id}/destroyAll',[ProductGalleryController::class,'destroyAllImages']);
    });


    // Product Attribute Routes :
    Route::prefix('product-attribute')->group(function () {
        Route::get('',[ProductAttributeController::class,'index'])->middleware('setLang');
        Route::get('/{id}', [ProductAttributeController::class, 'show'])->middleware('setLang');
        Route::post('',[ProductAttributeController::class,'store']);
        Route::post('{id}/update',[ProductAttributeController::class,'update']);
        Route::DELETE('{id}/delete',[ProductAttributeController::class,'destroy']);
    });


    // Product Attribute Value Routes :
    Route::prefix('product-attribute-value')->group(function () {
        Route::get('',[ProductAttributeValueController::class,'index'])->middleware('setLang');
        Route::get('/{id}', [ProductAttributeValueController::class, 'show'])->middleware('setLang');
        Route::post('',[ProductAttributeValueController::class,'store']);
        Route::post('{id}/update',[ProductAttributeValueController::class,'update']);
        Route::DELETE('{id}/delete',[ProductAttributeValueController::class,'destroy']);
    });

    //Product Type Routes :
    Route::prefix('product-type')->group(function () {
        Route::get('/',[ProductTypeController::class,'index'])->middleware('setLang');
        Route::get('/{id}', [ProductTypeController::class, 'show'])->middleware('setLang');
        Route::post('',[ProductTypeController::class,'store']);
        Route::post('{id}/update',[ProductTypeController::class,'update']);
        Route::DELETE('{id}/delete',[ProductTypeController::class,'destroy']);
    });
}); 