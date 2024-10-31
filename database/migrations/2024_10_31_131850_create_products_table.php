<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            /** this is the previous version of the products table  */
            // $table->id();
            // $table->text('thumb_image');//the main image of product we display it in the product card and be the first image always
            // $table->bigInteger('brand_id',false,true);
            // $table->integer('qty')->unsigned();// min:0 , max:10000
            // $table->text('video_link')->nullable();
            // $table->string('sku')->unique()->nullable();
            // $table->double('price')->unsigned();
            // $table->double('offer_price')->unsigned()->nullable();
            // $table->date('offer_start_date')->nullable();
            // $table->date('offer_end_date')->nullable();
            // $table->boolean('status')->default(1);

            // $table->timestamps();
            // $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        



            /** this is the new version of the products table [more efficient and effective] */
            
            $table->id();
            
            // Image - Consider using string instead of text for URLs
            $table->string('thumb_image', 255);  // text is excessive for image paths
            
            // Brand relationship
            $table->unsignedBigInteger('brand_id')->nullable();  // Changed from bigInteger to unsignedBigInteger

            $table->foreign('brand_id')
                    ->references('id')
                    ->on('brands')
                    ->onDelete('cascade');
            
            // Inventory
            $table->unsignedInteger('qty');  // Changed to unsignedInteger since you don't need bigInteger
            
            // Media
            $table->string('video_link', 255)->nullable();  // Changed from text to string
            
            // Product identifiers
            $table->string('sku', 50)->unique()->nullable();  // Added length constraint
            
            // Pricing
            $table->decimal('price', 10, 2)->unsigned();  // Changed from double to decimal for precise money handling
            $table->decimal('offer_price', 10, 2)->unsigned()->nullable();  // Changed from double to decimal
            
            // Offer dates
            $table->date('offer_start_date')->nullable();
            $table->date('offer_end_date')->nullable();
            
            // Status
            $table->boolean('status')->default(true);
            
            // Timestamps
            $table->timestamps();
            
            // Optional but recommended
            $table->softDeletes();
            
            // Add useful indexes
            $table->index('status');
            $table->index(['offer_start_date', 'offer_end_date']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
