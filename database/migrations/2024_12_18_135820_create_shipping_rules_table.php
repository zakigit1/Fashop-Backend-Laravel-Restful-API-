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
        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type',['flat','weight_based','value_based','tiered']);
            $table->decimal('min_cost', 10, 2)->unsigned()->nullable();
            $table->decimal('max_cost', 10, 2)->unsigned()->nullable();
            $table->decimal('cost',10,2)->unsigned();
            $table->decimal('weight_limit', 5, 2)->unsigned()->nullable();
            $table->boolean('status')->default(true);
            $table->string('region',100)->nullable();
            $table->string('region_hash');
            $table->text('description')->nullable();
            $table->string('carrier',100)->nullable();
            $table->string('delivery_time',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rules');
    }
};
