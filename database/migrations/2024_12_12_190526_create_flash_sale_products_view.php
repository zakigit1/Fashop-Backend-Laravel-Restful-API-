<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW flash_sale_products_view AS
            SELECT 
                p.id as product_id,
                MAX(CASE WHEN pt.locale = 'en' THEN pt.name END) as product_en,
                MAX(CASE WHEN pt.locale = 'fr' THEN pt.name END) as product_fr,
                MAX(CASE WHEN pt.locale = 'ar' THEN pt.name END) as product_ar
            FROM products p
            LEFT JOIN product_translations pt ON p.id = pt.product_id
            WHERE p.id NOT IN (SELECT product_id FROM flash_sale_items)
            AND p.status = 1
            GROUP BY p.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS flash_sale_products_view');
    }
};
