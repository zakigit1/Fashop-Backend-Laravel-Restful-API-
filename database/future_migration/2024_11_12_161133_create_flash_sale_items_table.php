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
        Schema::create('flash_sale_items', function (Blueprint $table) {
            $table->id();
           
            $table->unsignedBigInteger('product_id');

            // Flash Sale End Date: flash_sale_id
            $table->foreignId('flash_sale_id')->nullable()->constrained('flash_sales')->onDelete('set null');
            $table->boolean('show_at_home')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');  
            
            
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_items');
    }
};
