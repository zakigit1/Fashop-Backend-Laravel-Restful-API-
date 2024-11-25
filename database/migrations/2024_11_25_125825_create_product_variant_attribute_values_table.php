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
        Schema::create('product_variant_attribute_values', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            // $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_variant_id')->index();
            $table->unsignedBigInteger('attribute_value_id')->index();


            // Foreign key constraints
            $table->foreign('product_id')
            ->references('id')
                    ->on('products')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
                    
            $table->foreign('product_variant_id')
                    ->references('id')
                    ->on('product_variants')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade

            $table->foreign('attribute_value_id')
                    ->references('id')
                    ->on('attribute_values')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade




            // Ensure each attribute value is used only once per variant
            $table->unique(['product_id','product_variant_id', 'attribute_value_id'], 'product_variant_attribute_values_unique');
            // $table->unique(['product_variant_id', 'attribute_value_id'], 'product_variant_attribute_values_unique');

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
        Schema::dropIfExists('product_variant_attribute_values');
    }
};
