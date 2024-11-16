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
            // Using unsignedBigInteger is more appropriate than bigInteger for foreign keys
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('attribute_id')->index();
            $table->unsignedBigInteger('attribute_value_id')->index();
            $table->float('extra_price', 5, 2)->default(0.00)->unsigned(); // Additional price for this variant
            $table->integer('quantity')->default(0)->unsigned(); // Stock for this specific combination
            $table->boolean('is_default')->default(false);




            // Indices for better performance
            $table->index(['product_id', 'attribute_id']);
            $table->index(['is_default', 'product_id']);

            
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
                    ->on('attribute_values')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
                    


            // Composite unique index to prevent duplicate product-category combinations
            $table->unique(['product_id', 'attribute_id', 'attribute_value_id'], 'product_attribute_values_unique');


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
