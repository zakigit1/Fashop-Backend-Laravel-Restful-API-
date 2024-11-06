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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            // Using unsignedBigInteger is more appropriate than bigInteger for foreign keys
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('attribute_value_id');
            $table->decimal('extra_price', 10, 2)->default(0.00); // Additional price for this variant
            $table->integer('quantity')->default(0); // Stock for this specific combination
            $table->boolean('is_default')->default(false);






            // Composite unique index to prevent duplicate product-category combinations
            $table->unique(['product_id', 'attribute_id', 'attribute_value_id'], 'product_attribute_values_unique');
            
            // Foreign key constraints
            $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
                    
            $table->foreign('attribute_id')
                    ->references('id')
                    ->on('attributes')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade

            $table->foreign('attribute_value_id')
                    ->references('id')
                    ->on('attribute_value')
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
        Schema::dropIfExists('product_attributes');
    }
};
