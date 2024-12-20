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
        Schema::create('product_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained()->onDelete('cascade');

            $table->string('locale')->index();

            $table->string('name', 50);

            $table->unique(['product_type_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_type_translations');
    }
};
