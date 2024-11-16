<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('Attribute_values')->count() === 0) {
            DB::table('Attribute_values')->insert($this->Attribute_values());
        }
      
    }
    public function Attribute_values()
    {
        $attributeTypes = [
            'radio',
            'select',
            'color_picker'
            // Add more types as needed
        ];

        $attribute_values = [

            ####################################### Start Size Values ###############################################
                [
                    'attribute_id' => 1 ,
                    'name' => 'S',
                    'display_name' => 'Small',
                ],
                [
                    'attribute_id' => 1 ,
                    'name' => 'M',
                    'display_name' => 'Medium',
                ],
                [
                    'attribute_id' => 1 ,
                    'name' => 'L',
                    'display_name' => 'Large',
                ],
                [
                    'attribute_id' => 1 ,
                    'name' => 'XL',
                    'display_name' => 'Extra Large',
                ],
                [
                    'attribute_id' => 1 ,
                    'name' => 'XXL',
                    'display_name' => 'Double Extra Large',
                ],
                [
                    'attribute_id' => 1 ,
                    'name' => 'XXXL',
                    'display_name' => 'Triple Extra Large',
                ],
            ####################################### End Size Values ###############################################
            ####################################### Start Color Values ###############################################

                [
                    'attribute_id' => 2 ,
                    'name' => 'white',
                    'display_name' => 'White',
                    'color_code' => '#000000',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => 'black',
                    'display_name' => 'Black',
                    'color_code' => '#000000',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => 'black & white',
                    'display_name' => 'Black & White',
                    'color_code' => '#000000',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => 'red',
                    'display_name' => 'Red',
                    'color_code' => '#000000',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => 'blue',
                    'display_name' => 'Blue',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => 'purpale',
                    'display_name' => 'Purple',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => 'green',
                    'display_name' => 'Green',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],
                [
                    'attribute_id' => 2 ,
                    'name' => '',
                    'display_name' => '',
                    'color_code' => '#',
                ],

           
        ];

        return $attribute_values;
    }
}
