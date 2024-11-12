<?php

use App\Http\Controllers\Dashboard\Admin\AdminProfileController;
use App\Http\Controllers\Dashboard\Admin\BrandController;
use App\Http\Controllers\Dashboard\Admin\CategoryController;
use App\Http\Controllers\Dashboard\Admin\AttributeController;
use App\Http\Controllers\Dashboard\Admin\AttributeValueController;
use App\Http\Controllers\Dashboard\Admin\FlashSaleController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductAttributeValueController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductGalleryController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductTypeController;
use App\Http\Controllers\Dashboard\Admin\SliderController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|                   API Routes (-*- JUST FOR ADMIN -*-)
|--------------------------------------------------------------------------
|
*/



Route::group(['middleware'=>['admin-api:api'],],function () {

    //Admin Profile :
    Route::prefix('profiles')->group(function () {
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
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show'])->middleware('setLang');
        Route::post('/', [CategoryController::class, 'store']);
        
        // Route::put('/{id}', [CategoryController::class, 'update']);
        Route::post('/{id}/update', [CategoryController::class, 'update']);
        
        Route::delete('/{id}/delete', [CategoryController::class, 'destroy']);
    });


    // Product Routes :
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{id}', [ProductController::class, 'show'])->middleware('setLang');
        Route::post('/', [ProductController::class, 'store']);
        
        // Route::put('/{id}/update', [ProductController::class, 'update']);
        Route::post('/{id}/update', [ProductController::class, 'update']);
        
        Route::delete('/{id}/delete', [ProductController::class, 'destroy']);


        // Route::post('/{id}/product-attribute-values/add', [ProductController::class,'save_product_attribute_value']);
        // Route::post('/{id}/product-attribute-values/{attributeValueId}/update', [ProductController::class,'update_product_attribute_value']);
    });



    // Product Gallery Routes :
    Route::prefix('product-image-galleries')->group(function () {
        Route::get('',[ProductGalleryController::class,'index']);
        Route::post('',[ProductGalleryController::class,'store']);
        Route::DELETE('{id}/destroy',[ProductGalleryController::class,'destroy']);
        // Route::DELETE('{id}/destroyAll',[ProductGalleryController::class,'destroyAllImages']);
    });


    // Product Attribute Routes :
    Route::prefix('attributes')->group(function () {
        Route::get('',[AttributeController::class,'index']);
        Route::get('/{id}', [AttributeController::class, 'show'])->middleware('setLang');
        Route::post('',[AttributeController::class,'store']);
        Route::post('{id}/update',[AttributeController::class,'update']);
        Route::DELETE('{id}/delete',[AttributeController::class,'destroy']);
    });


    // Product Attribute Value Routes :
    Route::prefix('attribute-values')->group(function () {
        Route::get('',[AttributeValueController::class,'index']);
        Route::get('/{id}', [AttributeValueController::class, 'show'])->middleware('setLang');
        Route::post('',[AttributeValueController::class,'store']);
        Route::post('{id}/update',[AttributeValueController::class,'update']);
        Route::DELETE('{id}/delete',[AttributeValueController::class,'destroy']);
    });


    //Product Type Routes :
    Route::prefix('product-types')->group(function () {
        Route::get('/',[ProductTypeController::class,'index']);
        Route::get('/{id}', [ProductTypeController::class, 'show'])->middleware('setLang');
        Route::post('',[ProductTypeController::class,'store']);
        Route::post('{id}/update',[ProductTypeController::class,'update']);
        Route::DELETE('{id}/delete',[ProductTypeController::class,'destroy']);
    });


    
    //Product Attribute Value Routes : 
    Route::prefix('products-attributes-values')->group(function () {
        
        Route::get('/',[ProductAttributeValueController::class,'index']);
        Route::get('/{productId}',[ProductAttributeValueController::class,'show']);

        Route::post('/{productId}', [ProductAttributeValueController::class,'store']);
        // Route::put('/{id}/update', [ProductAttributeValueController::class,'update']);// when you finish all dashboard routes
        Route::post('/{id}/update/{productId}', [ProductAttributeValueController::class,'update']);
        Route::DELETE('/{id}/delete/{productId}', [ProductAttributeValueController::class,'destroy']);

    });


    // Slider Routes :
    Route::prefix('sliders')->group(function () {
        Route::get('',[SliderController::class,'index']);
        Route::get('/{id}', [SliderController::class, 'show'])->middleware('setLang');
        Route::post('',[SliderController::class,'store']);
        Route::post('{id}/update',[SliderController::class,'update']);
        Route::DELETE('{id}/delete',[SliderController::class,'destroy']);
    });


    Route::prefix('flash-sales')->group(function () {
    
        Route::get('/',[FlashSaleController::class,'index']);
        Route::post('/end-date',[FlashSaleController::class,'end_date']);
        Route::post('/add-product',[FlashSaleController::class,'add_product']);
        Route::DELETE('{id}/destroy',[FlashSaleController::class,'destroy']);
        Route::put('change-at-home-status',[FlashSaleController::class,'change_at_home_status']);

    });




}); 