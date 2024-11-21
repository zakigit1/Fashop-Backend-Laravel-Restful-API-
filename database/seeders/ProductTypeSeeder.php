<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Schema::hasTable('product_types')){
            if (DB::table('product_types')->count() === 0) {
                DB::table('product_types')->insert($this->productTypes());
            }
        }

        if(Schema::hasTable('product_type_translations')){
            if (DB::table('product_type_translations')->count() === 0) {
                DB::table('product_type_translations')->insert($this->productTypeTranslations());
            }    
        }    
    }

    public function productTypes()
    {
        $productTypes = [

            [
                'id' => 1,            
                'status' => 1 ,
            ],
            [
                'id' => 2,
                'status' => 1 ,
            ],
            [
                'id' => 3,
                'status' => 1 ,
            ],
            [
                'id' => 4,
                'status' => 1 ,
            ],
            [
                'id' => 5,
                'status' => 1 ,
            ],
            [
                'id' => 6,
                'status' => 1 ,
            ],
            [
                'id' => 7,
                'status' => 1 ,
            ],
            [
                'id' => 8,
                'status' => 1 ,
            ],
            [
                'id' => 9,
                'status' => 1 ,
            ],
            [
                'id' => 10,
                'status' => 1,
            ],

        ];

        return $productTypes;
    }
    public function productTypeTranslations()
    {
        $locales = [
            'en',
            'ar',
            'fr',
        ];

        $productTypeTranslations = [
            [
                'product_type_id' => 1,
                'translations' => [
                    'en' => 'Best Sellers',
                    'ar' => 'الأكثر مبيعاً',
                    'fr' => 'Meilleures ventes',
                ],
            ],
            [
                'product_type_id' => 2,
                'translations' => [
                    'en' => 'Featured Products',
                    'ar' => 'منتجات مميزة',
                    'fr' => 'Produits special',
                ],
            ],
            [
                'product_type_id' => 3,
                'translations' => [
                    'en' => 'New Arrivals',
                    'ar' => 'وصل حديثاً',
                    'fr' => 'Nouveautés',
                ],
            ],
            [
                'product_type_id' => 4,
                'translations' => [
                    'en' => 'On Sale',
                    'ar' => 'تخفيضات',
                    'fr' => 'En Solde',
                ],
            ],
            [
                'product_type_id' => 5,
                'translations' => [
                    'en' => 'Tendance',
                    'ar' => 'رائج',
                    'fr' => 'Trending',
                ],
            ],

            [
                'product_type_id' => 6,
                'translations' => [
                    'en' => 'Limited Edition',
                    'ar' => 'إصدار محدود',
                    'fr' => 'Édition limitée',
                ],
            ],
            [
                'product_type_id' => 7,
                'translations' => [
                    'en' => 'Seasonal',
                    'ar' => 'موسمي',
                    'fr' => 'Saisonnier',
                ],
            ],
            [
                'product_type_id' => 8,
                'translations' => [
                    'en' => 'Top Rated',
                    'ar' => 'الأعلى تقييماً',
                    'fr' => 'Les mieux notés',
                ],
            ],
            [
                'product_type_id' => 9,
                'translations' => [
                    'en' => 'Clearance',
                    'ar' => 'تصفية',
                    'fr' => 'Liquidation',
                ],
            ],


            [
                'product_type_id' => 10,
                'translations' => [ 
                    'en' => 'Recommended', 
                    'ar' => 'موصى', 
                    'fr' => 'Recommandé', 
                ],

            ],

        ];
            

             


        $result = [];
        foreach ($productTypeTranslations as $productTypeTranslation) {
            foreach ($locales as $locale) {
                $result[] = [
                    'product_type_id' => $productTypeTranslation['product_type_id'],
                    'locale' => $locale,
                    'name' => $productTypeTranslation['translations'][$locale],
                ];
            }
        }

        return $result;
    }
}
// Best Sellers: These are the most popular items based on sales.

// Featured Products: Highlighted items that are promoted on the main page.

// New Arrivals: Recently added products to the store.

// On Sale: Items that are currently discounted.

// Trending: Products that are currently popular or in high demand.

// Limited Edition: Exclusive items available for a short period.

// Seasonal: Products that are relevant to the current season (e.g., summer, winter).

// Top Rated: Items with the highest customer ratings.

// Clearance: Products that are being sold at a reduced price to clear out inventory.

