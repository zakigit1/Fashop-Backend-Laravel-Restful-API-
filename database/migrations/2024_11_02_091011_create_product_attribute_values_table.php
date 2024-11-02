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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('attribute_value_id');
            $table->decimal('extra_price', 10, 2)->default(0.00); // Additional price for this variant
            $table->integer('quantity')->default(0); // Stock for this specific combination
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
                  
            $table->foreign('attribute_id')
                  ->references('id')
                  ->on('attributes')
                  ->onDelete('cascade');
                  
            $table->foreign('attribute_value_id')
                  ->references('id')
                  ->on('attribute_values')
                  ->onDelete('cascade');
                  
            // Ensure unique combinations
            // $table->unique(['product_id', 'attribute_id', 'attribute_value_id']);

            $table->unique(['product_id', 'attribute_id', 'attribute_value_id'], 'product_attribute_values_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
