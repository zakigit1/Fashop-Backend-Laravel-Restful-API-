<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // if (DB::table('Attributes')->count() === 0) {
        //     DB::table('Attributes')->insert($this->attributes());
        // }
        // if (DB::table('Attribute_Translations')->count() === 0) {
        //     DB::table('Attribute_Translations')->insert($this->attributeTranslations());
        // }
        
        DB::table('Attributes')->insert($this->attributes());

        DB::table('Attribute_Translations')->insert($this->attributeTranslations());
        
    }

    public function attributes(){

        return [
             [

                'status'=>'admin@gmail.com',
                // $table->string('type', 20)->default('select'); // select, radio, color_picker, etc.
                // $table->boolean('is_required')->default(false);
                // $table->boolean('is_filterable')->default(true); // Can be used in product filters?
                // $table->integer('sort_order')->default(0);
                // $table->boolean('status')->default(true);
             ],
             [
                'name'=>'user',
                'username'=>'user',
                'email'=>'user@gmail.com',
                'role'=>'user',
                'password'=>bcrypt('password'),
             ]
         ];
    }


    public function attributeTranslations(){
       
        return [
            //Size
            [
                [
                    'attribute_id' => 1,
                    'locale' => 'en',
                    'name' => 'Size',
                ],
                [
                    'attribute_id' => 1,
                    'locale' => 'ar',
                    'name' => 'الحجم',
                ],
                [
                    'attribute_id' => 1,
                    'locale' => 'fr',
                    'name' => 'Taille',
                ],
            ],

            //Color
            [
                [
                    'attribute_id' => 2,
                    'locale' => 'en',
                    'name' => 'Color',
                ],
                [
                    'attribute_id' => 2,
                    'locale' => 'ar',
                    'name' => 'اللون',
                ],
                [
                    'attribute_id' => 2,
                    'locale' => 'fr',
                    'name' => 'Couleur',
                ],
            ],

            //Material
            [
                [
                    'attribute_id' => 3,
                    'locale' => 'en',
                    'name' => 'Material',
                ],
                [
                    'attribute_id' => 3,
                    'locale' => 'ar',
                    'name' => 'المواد',
                ],
                [
                    'attribute_id' => 3,
                    'locale' => 'fr',
                    'name' => 'Matériau',
                ],
            ],

            //Fit Type
            [
                [
                    'attribute_id' => 4,
                    'locale' => 'en',
                    'name' => 'Fit Type',
                ],
                [
                    'attribute_id' => 4,
                    'locale' => 'ar',
                    'name' => 'نوع الملاءمة',
                ],
                [
                    'attribute_id' => 4,
                    'locale' => 'fr',
                    'name' => 'Type d\'ajustement',
                ],
            ],
            //Pattern
            [
                [
                    'attribute_id' => 5,
                    'locale' => 'en',
                    'name' => 'Pattern',
                ],
                [
                    'attribute_id' => 5,
                    'locale' => 'ar',
                    'name' => 'النمط',
                ],
                [
                    'attribute_id' => 5,
                    'locale' => 'fr',
                    'name' => 'Modèle',
                ],
            ],  
    
            //Length
            [
                [
                    'attribute_id' => 6,
                    'locale' => 'en',
                    'name' => 'Length',
                ],
                [
                    'attribute_id' => 6,
                    'locale' => 'ar',
                    'name' => 'الطول',
                ],
                [
                    'attribute_id' => 6,
                    'locale' => 'fr',
                    'name' => 'Longueur',
                ],
            ],  
    
        ];



        // $attribute->translateOrNew(en)->name = 'Size';
        // $attribute->translateOrNew(ar)->name = 'الحجم';
        // $attribute->translateOrNew(fr)->name = 'Taille';

        // $attribute->translateOrNew(en)->name = 'Color';
        // $attribute->translateOrNew(ar)->name = 'اللون';
        // $attribute->translateOrNew(fr)->name = 'Couleur';

        // $attribute->translateOrNew(en)->name = 'Material';
        // $attribute->translateOrNew(ar)->name = 'المواد';
        // $attribute->translateOrNew(fr)->name = 'Matériau';

        // $attribute->translateOrNew(en)->name = 'Fit Type';
        // $attribute->translateOrNew(ar)->name = 'نوع الملاءمة';
        // $attribute->translateOrNew(fr)->name = 'Type d\'ajustement';

        // $attribute->translateOrNew(en)->name = 'Pattern';
        // $attribute->translateOrNew(ar)->name = 'النمط';
        // $attribute->translateOrNew(fr)->name = 'Modèle';

        // $attribute->translateOrNew(en)->name = 'Length';
        // $attribute->translateOrNew(ar)->name = 'الطول';
        // $attribute->translateOrNew(fr)->name = 'Longueur';
        
    }
}
