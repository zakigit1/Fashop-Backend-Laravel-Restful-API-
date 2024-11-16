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

            $table->string('name',255);
            $table->string('type',50);
            $table->decimal('min_cost', 10, 2)->unsigned()->nullable();
            // $table->decimal('max_cost', 10, 2)->unsigned()->nullable();
            $table->decimal('cost',10,2)->unsigned();
            // $table->decimal('weight_limit', 10, 2)->unsigned()->nullable();
            $table->boolean('status')->default(true);
            // $table->text('description')->nullable();
            // $table->string('region',100)->nullable();
            // $table->string('carrier',100)->nullable();
            // $table->string('delivery_time',50)->nullable();
            
            $table->timestamps();
        });


        // id: Unique identifier for each shipping rule.

        // name: Descriptive name for the shipping rule.

        // type: Type of shipping rule (e.g., flat, min_cost, weight-based).

        // min_cost: Minimum order value for the rule to apply (relevant for min_cost type).

        // max_cost: Maximum order value for the rule to apply (useful for tiered shipping).

        // cost: Shipping cost associated with the rule.

        // weight_limit: Maximum weight for the rule to apply (useful for weight-based shipping).

        // status: Indicates if the rule is active or inactive.

        // created_at: Timestamp for when the rule was created.

        // updated_at: Timestamp for when the rule was last updated.

        // description: A brief description of the shipping rule.

        // region: The geographical region where the shipping rule applies.

        // carrier: The shipping carrier associated with the rule.

        // delivery_time: Estimated delivery time for the shipping option.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rules');
    }
};
