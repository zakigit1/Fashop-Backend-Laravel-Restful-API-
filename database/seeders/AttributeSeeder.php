<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Schema::hasTable('attributes')){
            if (DB::table('attributes')->count() === 0) {
                DB::table('attributes')->insert($this->attributes());
            }
        }

        if(Schema::hasTable('attribute_translations')){
            if (DB::table('attribute_translations')->count() === 0) {
                DB::table('attribute_translations')->insert($this->attributeTranslations());
            }    
        }    
    }


 
    public function attributes()
    {
        $attributeTypes = [
            'radio',
            'select',
            'color_picker'
            // Add more types as needed
        ];

        $attributes = [
            [
                'id' => 1,            
                'type' => $attributeTypes[0],
            ],
            [
                'id' => 2,
                'type' => $attributeTypes[0],
            ],
            [
                'id' => 3,
                'type' => $attributeTypes[0],
            ],
            [
                'id' => 4,
                'type' => $attributeTypes[0],
            ],
            [
                'id' => 5,
                'type' => $attributeTypes[0],
            ],
            [
                'id' => 6,
                'type' => $attributeTypes[0],
            ],
            // [
            //     'id' => 7,
            //     'type' => $attributeTypes[0],
            // ],
            // [
            //     'id' => 8,
            //     'type' => $attributeTypes[0],
            // ],
            // [
            //     'id' => 9,
            //     'type' => $attributeTypes[0],
            // ],
            // [
            //     'id' => 10,
            //     'type' => $attributeTypes[0],
            // ],
            // [
            //     'id' => 11,
            //     'type' => $attributeTypes[0],
            // ],
        ];

        return $attributes;
    }

    public function attributeTranslations()
    {
        $locales = [
            'en',
            'ar',
            'fr',
        ];

        $attributeTranslations = [
            [
                'attribute_id' => 1,
                'translations' => [
                    'en' => 'Size',
                    'ar' => 'الحجم',
                    'fr' => 'Taille',
                ],
            ],
            [
                'attribute_id' => 2,
                'translations' => [
                    'en' => 'Color',
                    'ar' => 'اللون',
                    'fr' => 'Couleur',
                ],
            ],
            [
                'attribute_id' => 3,
                'translations' => [
                    'en' => 'Material',
                    'ar' => 'المواد',
                    'fr' => 'Matériau',
                ],
            ],
            [
                'attribute_id' => 4,
                'translations' => [
                    'en' => 'Fit Type',
                    'ar' => 'نوع الملاءمة',
                    'fr' => 'Type d\'ajustement',
                ],
            ],
            [
                'attribute_id' => 5,
                'translations' => [
                    'en' => 'Pattern',
                    'ar' => 'النمط',
                    'fr' => 'Modèle',
                ],
            ],

            [
                'attribute_id' => 6,
                'translations' => [
                    'en' => 'Numeric Size',
                    'ar' => 'الحجم الرقمي',
                    'fr' => 'Taille numérique',
                ],
            ],

            
            // [
            //     'attribute_id' => 7,
            //     'translations' => [
            //         'en' => 'Length',
            //         'ar' => 'الطول',
            //         'fr' => 'Longueur',
            //     ],
            // ],

            // [
            //     'attribute_id' => 8,
            //     'translations' => [
            //         'en' => 'Sleeve Length',
            //         'ar' => 'طول الأكمام',
            //         'fr' => 'Longueur de la manche',
            //     ],
            // ],
            // [
            //     'attribute_id' => 9,
            //     'translations' => [
            //         'en' => 'Neckline',
            //         'ar' => 'خط العنق',
            //         'fr' => 'Encolure',
            //     ],
            // ],
            // [
            //     'attribute_id' => 10,
            //     'translations' => [
            //         'en' => 'Occasion',
            //         'ar' => 'المناسبة',
            //         'fr' => 'Occasion',
            //     ],
            // ],
            // [
            //     'attribute_id' => 11,
            //     'translations' => [
            //         'en' => 'Fabric Care',
            //         'ar' => 'العناية بالأقمشة',
            //         'fr' => 'Entretien du tissu',
            //     ],
            // ],
        ];

        $result = [];
        foreach ($attributeTranslations as $attributeTranslation) {
            foreach ($locales as $locale) {
                $result[] = [
                    'attribute_id' => $attributeTranslation['attribute_id'],
                    'locale' => $locale,
                    'name' => $attributeTranslation['translations'][$locale],
                ];
            }
        }

        return $result;
    }
}
