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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('attribute_id');
            $table->string('name', 100);  // S, M, L or Red, Blue, etc.
            $table->string('display_name', 100)->nullable();  // S = in column display value it is Small
            $table->string('color_code', 20)->nullable(); // For color attributes
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();


            $table->unique(['attribute_id', 'name']);
            
            $table->foreign('attribute_id')
                  ->references('id')
                  ->on('attributes')
                  ->onDelete('cascade');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
