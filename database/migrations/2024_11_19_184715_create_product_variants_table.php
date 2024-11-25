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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('extra_price', 10, 2)->default(0.00);
            $table->decimal('final_price', 10, 2); // Calculated: product base price + extra_price
            $table->integer('quantity')->unsigned()->default(0);
            $table->string('sku', 50)->unique()->nullable();  // Added length constraint
            $table->string('barcode', 255)->nullable(); // you can add UPC and EAN (search about it)
            $table->boolean('in_stock')->default(true);
            $table->string('variant_hash')->unique(); 
            $table->timestamps();

            $table->foreign('product_id')
            ->references('id')
                    ->on('products')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
