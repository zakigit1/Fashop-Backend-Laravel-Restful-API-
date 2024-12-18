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
        Schema::create('shipping_rule_regions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shipping_rule_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            
            
            $table->unique(['shipping_rule_id', 'country_id','city_id'],'shipping_rule_country_city_unique');
            
            
            $table->foreign('shipping_rule_id')
                    ->references('id')
                    ->on('shipping_rules')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
                    
            $table->foreign('country_id')
                    ->references('id')
                    ->on('countries')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
                    
            $table->foreign('city_id')
                    ->references('id')
                    ->on('cities')
                    ->onDelete('cascade')
                    ->onUpdate('cascade'); // Added onUpdate cascade
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rule_regions');
    }
};
