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
        Schema::create('stripe_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(false);
            $table->string('mode');
            $table->string('country_name');
            $table->string('currency_name');
            $table->float('currency_rate', 5, 2)->unsigned()->default(1.00);
            $table->text('client_id');
            $table->text('secret_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_settings');
    }
};
