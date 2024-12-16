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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('max_use');
            $table->date('start_date');
            $table->date('end_date');

            // $table->string('discount_type');
            $table->enum('discount_type', ['amount', 'percentage']);

            $table->float('discount', 10, 2)->unsigned();// 10 is total number of digits, 2 is number of decimal places
            $table->boolean('status');
            $table->unsignedInteger('total_used');

            $table->float('min_purchase_amount', 10, 2)->unsigned()->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
