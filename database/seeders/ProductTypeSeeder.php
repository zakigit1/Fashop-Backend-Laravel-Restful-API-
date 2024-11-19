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
                    'en' => 'Size',
                    'ar' => 'الحجم',
                    'fr' => 'Taille',
                ],
            ],
            [
                'product_type_id' => 2,
                'translations' => [
                    'en' => 'Color',
                    'ar' => 'اللون',
                    'fr' => 'Couleur',
                ],
            ],
            [
                'product_type_id' => 3,
                'translations' => [
                    'en' => 'Material',
                    'ar' => 'المواد',
                    'fr' => 'Matériau',
                ],
            ],
            [
                'product_type_id' => 4,
                'translations' => [
                    'en' => 'Fit Type',
                    'ar' => 'نوع الملاءمة',
                    'fr' => 'Type d\'ajustement',
                ],
            ],
            [
                'product_type_id' => 5,
                'translations' => [
                    'en' => 'Pattern',
                    'ar' => 'النمط',
                    'fr' => 'Modèle',
                ],
            ],

            [
                'product_type_id' => 6,
                'translations' => [
                    'en' => 'Numeric Size',
                    'ar' => 'الحجم الرقمي',
                    'fr' => 'Taille numérique',
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
