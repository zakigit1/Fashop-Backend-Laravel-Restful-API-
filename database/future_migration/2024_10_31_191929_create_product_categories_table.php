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
        Schema::create('product_categories', function (Blueprint $table) {
            
            /** this is the previous version of the product_categories table  */
            // $table->id();
            // $table->bigInteger('product_id',false,true);
            // $table->bigInteger('category_id',false,true);
            // $table->unique(['product_id', 'category_id']);
            // $table->timestamps();
            
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');


            /** this is the new version of the product_categories table [more efficient and effective] */
            
            $table->id();
            // Using unsignedBigInteger is more appropriate than bigInteger for foreign keys
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id');
            
            // Composite unique index to prevent duplicate product-category combinations
            $table->unique(['product_id', 'category_id']);
            
            // Foreign key constraints
            $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
                    
            $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
                    
            // Optional: Add additional audit fields
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
