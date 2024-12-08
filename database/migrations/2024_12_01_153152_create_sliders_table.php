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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('image',255)->nullable();
            $table->unsignedInteger('order')->unique();
            $table->boolean('status');

            // $table->string('background_color')->default('#FFFFFF');
            // $table->string('title_color')->default('#000000');
            // $table->string('description_color')->default('#000000');

            $table->string('button_link')->nullable();
            // like : shop now button
            // $table->string('button_text',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
