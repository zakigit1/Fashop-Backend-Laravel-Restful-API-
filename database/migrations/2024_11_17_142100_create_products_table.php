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
            
            $table->id();
            
            // Image - Consider using string instead of text for URLs
            $table->string('thumb_image', 255);  // text is excessive for image paths
            
            // Brand relationship
            $table->unsignedBigInteger('brand_id')->nullable();  // Changed from bigInteger to unsignedBigInteger

            $table->foreign('brand_id')
                    ->references('id')
                    ->on('brands')
                    ->onDelete('cascade');

            // Product Type relationship
            $table->unsignedBigInteger('product_type_id')->nullable();  // Changed from bigInteger to unsignedBigInteger

            $table->foreign('product_type_id')
                    ->references('id')
                    ->on('product_types')
                    ->onDelete('cascade');
            
            // Inventory
            $table->unsignedInteger('qty');  // Changed to unsignedInteger since you don't need bigInteger
            
            $table->unsignedInteger('variant_quantity');  // increment and decrement the quantity depending on variant quantity 
            
            // Media
            $table->string('video_link', 255)->nullable();  // Changed from text to string
            

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
