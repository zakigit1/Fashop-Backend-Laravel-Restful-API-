<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttributeValueSeeder extends Seeder
{
    const ATTRIBUTE_TYPES = [
        'size' => 1,
        'color' => 2,
        'material' => 3,
        'fit_type' => 4,
        'pattern' => 5,
        'numeric_size' => 6,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Schema::hasTable('attribute_values')) {
            if (DB::table('attribute_values')->count() === 0) {
                DB::table('attribute_values')->insert($this->attributeValues());
            }
        }
    }

    public function attributeValues()
    {
        $attributeValues = [];

        // Size Values
        $attributeValues[] = $this->getSizeValues();

        // Color Values
        $attributeValues[] = $this->getColorValues();

        // Material Values
        $attributeValues[] = $this->getMaterialValues();

        // Fit Type Values
        $attributeValues[] = $this->getFitTypeValues();

        // Pattern Values
        $attributeValues[] = $this->getPatternValues();

        // Numeric Size Values
        $attributeValues[] = $this->getNumericSizeValues();

        // Merge all attribute values into a single array
        $attributeValues = array_merge(...$attributeValues);

        return $attributeValues;
    }

    private function getSizeValues()
    {
        return [
            ['attribute_id' => self::ATTRIBUTE_TYPES['size'], 'name' => 'S', 'display_name' => 'Small', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['size'], 'name' => 'M', 'display_name' => 'Medium', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['size'], 'name' => 'L', 'display_name' => 'Large', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['size'], 'name' => 'XL', 'display_name' => 'Extra Large', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['size'], 'name' => 'XXL', 'display_name' => 'Double Extra Large', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['size'], 'name' => 'XXXL', 'display_name' => 'Triple Extra Large', 'color_code' => null],
        ];
    }

    private function getColorValues()
    {
        return [
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'white', 'display_name' => 'White', 'color_code' => '#FFFFFF'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'black', 'display_name' => 'Black', 'color_code' => '#000000'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'red', 'display_name' => 'Red', 'color_code' => '#FF0000'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'blue', 'display_name' => 'Blue', 'color_code' => '#0000FF'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'purple', 'display_name' => 'Purple', 'color_code' => '#800080'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'green', 'display_name' => 'Green', 'color_code' => '#008000'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'yellow', 'display_name' => 'Yellow', 'color_code' => '#FFFF00'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'pink', 'display_name' => 'Pink', 'color_code' => '#FFC0CB'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'orange', 'display_name' => 'Orange', 'color_code' => '#FFA500'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'brown', 'display_name' => 'Brown', 'color_code' => '#A52A2A'],
            ['attribute_id' => self::ATTRIBUTE_TYPES['color'], 'name' => 'gray', 'display_name' => 'Gray', 'color_code' => '#808080'],
        ];
    }

    private function getMaterialValues()
    {
        return [
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'cotton', 'display_name' => 'Cotton', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'wool', 'display_name' => 'Wool', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'silk', 'display_name' => 'Silk', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'polyester', 'display_name' => 'Polyester', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'linen', 'display_name' => 'Linen', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'denim', 'display_name' => 'Denim', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'leather', 'display_name' => 'Leather', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'nylon', 'display_name' => 'Nylon', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'rayon', 'display_name' => 'Rayon', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['material'], 'name' => 'spandex', 'display_name' => 'Spandex', 'color_code' => null],
        ];
    }

    private function getFitTypeValues()
    {
        return [
            ['attribute_id' => self::ATTRIBUTE_TYPES['fit_type'], 'name' => 'slim', 'display_name' => 'Slim Fit', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['fit_type'], 'name' => 'regular', 'display_name' => 'Regular Fit', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['fit_type'], 'name' => 'loose', 'display_name' => 'Loose Fit', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['fit_type'], 'name' => 'tailored', 'display_name' => 'Tailored Fit', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['fit_type'], 'name' => 'athletic', 'display_name' => 'Athletic Fit', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['fit_type'], 'name' => 'relaxed', 'display_name' => 'Relaxed Fit', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['fit_type'], 'name' => 'oversized', 'display_name' => 'Oversized Fit', 'color_code' => null],
        ];
    }

    private function getPatternValues()
    {
        return [
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'stripes', 'display_name' => 'Stripes', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'plaid', 'display_name' => 'Plaid', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'floral', 'display_name' => 'Floral', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'polka dots', 'display_name' => 'Polka Dots', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'animal print', 'display_name' => 'Animal Print', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'geometric', 'display_name' => 'Geometric', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'paisley', 'display_name' => 'Paisley', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'houndstooth', 'display_name' => 'Houndstooth', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'chevron', 'display_name' => 'Chevron', 'color_code' => null],
            ['attribute_id' => self::ATTRIBUTE_TYPES['pattern'], 'name' => 'argyle', 'display_name' => 'Argyle', 'color_code' => null],
        ];
    }

    private function getNumericSizeValues()
    {
        $sizes = range(18, 52);
        $attribute_values = [];
    
        foreach ($sizes as $size) {
            $attribute_values[] = [
                'attribute_id' => self::ATTRIBUTE_TYPES['numeric_size'],
                'name' => $size,
                'display_name' => $size,
                'color_code' => null,
            ];
        }
    
        return $attribute_values;
    }

    public function Attribute_values()
    {
        $attribute_values = array_merge(
            $this->getSizeValues(),
            $this->getColorValues(),
            $this->getMaterialValues(),
            $this->getFitTypeValues(),
            $this->getPatternValues(),
            $this->getNumericSizeValues()
        );

        return $attribute_values;
    }
}