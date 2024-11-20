<?php

use App\Http\Controllers\Dashboard\Admin\AdminProfileController;
use App\Http\Controllers\Dashboard\Admin\BrandController;
use App\Http\Controllers\Dashboard\Admin\CategoryController;
use App\Http\Controllers\Dashboard\Admin\AttributeController;
use App\Http\Controllers\Dashboard\Admin\AttributeValueController;
use App\Http\Controllers\Dashboard\Admin\CouponController;
use App\Http\Controllers\Dashboard\Admin\FlashSaleController;
use App\Http\Controllers\Dashboard\Admin\Payment\Gatways\CODSettingController;
use App\Http\Controllers\Dashboard\Admin\Payment\Gatways\PaypalSettingController;
use App\Http\Controllers\Dashboard\Admin\Payment\Gatways\StripeSettingController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductAttributeValueController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductGalleryController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductNewController;
use App\Http\Controllers\Dashboard\Admin\Product\ProductTypeController;
use App\Http\Controllers\Dashboard\Admin\ShippingRuleController;
use App\Http\Controllers\Dashboard\Admin\SliderController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|                   API Routes (-*- JUST FOR ADMIN -*-)
|--------------------------------------------------------------------------
|
*/



Route::group(['middleware'=>['admin-api:api'],],function () {

    //Admin Profile ://! you need to test this endpoint
    Route::prefix('profiles')->group(function () {
        Route::post('/update', [AdminProfileController::class, 'update_profile']);
        // Route::put('/update', [AdminProfileController::class, 'update_profile']);
        Route::post('/update/password', [AdminProfileController::class, 'update_profile_password']);
        // Route::put('/update/password', [AdminProfileController::class, 'update_profile_password']);
    });
      
    // Brand Routes :
    Route::prefix('brands')->group(function () {
        // Route::get('/', [BrandController::class, 'index'])->middleware('setLang');
        Route::get('/', [BrandController::class, 'index']);
        Route::get('/{id}', [BrandController::class, 'show'])->middleware('setLang');
        Route::post('/', [BrandController::class, 'store']);

      
        // Route::put('/{id}', [BrandController::class, 'update']);
        Route::post('/{id}/update', [BrandController::class, 'update']);
        
        Route::delete('/{id}/delete', [BrandController::class, 'destroy']);
        // Route::delete('/{id}', [BrandController::class, 'destroy']);
    });

    
    // Category Routes :
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show'])->middleware('setLang');
        Route::post('/', [CategoryController::class, 'store']);
        
        Route::post('/{id}/update', [CategoryController::class, 'update']);
        // Route::put('/{id}', [CategoryController::class, 'update']);
        
        Route::delete('/{id}/delete', [CategoryController::class, 'destroy']);
        // Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });


    // Product Routes (V2) :
    // Route::prefix('products')->group(function () {
    //     Route::get('/', [ProductController::class, 'index']);
    //     Route::get('/{id}', [ProductController::class, 'show'])->middleware('setLang');
    //     Route::post('/', [ProductController::class, 'store']);
        
    //     Route::post('/{id}/update', [ProductController::class, 'update']);
    //     // Route::put('/{id}', [ProductController::class, 'update']);
        
    //     Route::delete('/{id}/delete', [ProductController::class, 'destroy']);
    //     // Route::delete('/{id}', [ProductController::class, 'destroy']);

    //     //! you need to add other route about soft deletes (restore , forceDelete)

    //     // Route::post('/{id}/product-attribute-values/add', [ProductController::class,'save_product_attribute_value']);
    //     // Route::post('/{id}/product-attribute-values/{attributeValueId}/update', [ProductController::class,'update_product_attribute_value']);
    // });


    // Product Routes (New) :
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductNewController::class, 'index']);
        Route::post('/', [ProductNewController::class, 'store']);
        Route::post('/{id}/update', [ProductNewController::class, 'update']);
        // Route::put('/{id}', [ProductNewController::class, 'update']);
        Route::delete('/{id}', [ProductNewController::class, 'destroy']);
    });



    // Product Gallery Routes :
    Route::prefix('product-image-galleries')->group(function () {
        Route::get('',[ProductGalleryController::class,'index']);
        Route::post('',[ProductGalleryController::class,'store']);
        Route::DELETE('{id}/destroy',[ProductGalleryController::class,'destroy']);
        // Route::DELETE('{id}',[ProductGalleryController::class,'destroy']);
        // Route::DELETE('{id}/destroyAll',[ProductGalleryController::class,'destroyAllImages']);
    });

    
    // Product Attribute Routes :
    Route::prefix('attributes')->group(function () {
        Route::get('',[AttributeController::class,'index']);
        Route::get('/{id}', [AttributeController::class, 'show'])->middleware('setLang');
        Route::post('',[AttributeController::class,'store']);
        Route::post('{id}/update',[AttributeController::class,'update']);
        // Route::put('{id}',[AttributeController::class,'update']);
        Route::DELETE('{id}/delete',[AttributeController::class,'destroy']);
        // Route::DELETE('{id}',[AttributeController::class,'destroy']);
    });


    // Product Attribute Value Routes :
    Route::prefix('attribute-values')->group(function () {
        Route::get('',[AttributeValueController::class,'index']);
        Route::get('/{id}', [AttributeValueController::class, 'show'])->middleware('setLang');
        Route::post('',[AttributeValueController::class,'store']);
        Route::post('{id}/update',[AttributeValueController::class,'update']);
        // Route::put('{id}',[AttributeValueController::class,'update']);
        Route::DELETE('{id}/delete',[AttributeValueController::class,'destroy']);
        // Route::DELETE('{id}',[AttributeValueController::class,'destroy']);
    });


    //Product Type Routes :
    Route::prefix('product-types')->group(function () {
        Route::get('/',[ProductTypeController::class,'index']);
        Route::get('/{id}', [ProductTypeController::class, 'show'])->middleware('setLang');
        Route::post('',[ProductTypeController::class,'store']);
        Route::post('{id}/update',[ProductTypeController::class,'update']);
        // Route::put('{id}',[ProductTypeController::class,'update']);
        Route::DELETE('{id}/delete',[ProductTypeController::class,'destroy']);
        // Route::DELETE('{id}',[ProductTypeController::class,'destroy']);
    });


    //Product Attribute Value Routes: 
    Route::prefix('products-attributes-values')->group(function () {
        
        Route::get('/',[ProductAttributeValueController::class,'index']);
        Route::get('/{productId}',[ProductAttributeValueController::class,'show']);

        Route::post('/{productId}', [ProductAttributeValueController::class,'store']);

        Route::post('/{id}/update/{productId}', [ProductAttributeValueController::class,'update']);
        // Route::put('/{id}/update/{productId}', [ProductAttributeValueController::class,'update']);// when you finish all dashboard routes
        
        Route::DELETE('/{id}/delete/{productId}', [ProductAttributeValueController::class,'destroy']);

    });


    // Slider Routes :
    Route::prefix('sliders')->group(function () {
        Route::get('',[SliderController::class,'index']);
        Route::get('/{id}', [SliderController::class, 'show'])->middleware('setLang');
        Route::post('',[SliderController::class,'store']);
        Route::post('{id}/update',[SliderController::class,'update']);
        // Route::put('{id}',[SliderController::class,'update']);
        Route::DELETE('{id}/delete',[SliderController::class,'destroy']);
        // Route::DELETE('{id}',[SliderController::class,'destroy']);
    });

    // Flash Sale Routes :
    Route::prefix('flash-sales')->group(function () {
    
        Route::get('/',[FlashSaleController::class,'index']);
        Route::post('/end-date',[FlashSaleController::class,'end_date']);
        // Route::put('/end-date',[FlashSaleController::class,'end_date']);
        Route::post('/add-product',[FlashSaleController::class,'add_product']);
        Route::DELETE('{id}/delete',[FlashSaleController::class,'destroy']);
        // Route::DELETE('{id}',[FlashSaleController::class,'destroy']);
    });

    // Coupon Routes :
    Route::apiResource('coupons', CouponController::class);

    // Shipping Rule Routes :
    Route::apiResource('shipping-rules', ShippingRuleController::class);


    /** Payment Gatways Settings Routes :  */

    // Paypal Setting Routes :
    Route::apiResource('paypal-settings', PaypalSettingController::class);
    
    // Stripe Setting Routes :
    Route::apiResource('stripe-settings', StripeSettingController::class);
    
    // Cash On Delivery Setting Routes :
    Route::apiResource('cod-settings', CODSettingController::class);


}); 