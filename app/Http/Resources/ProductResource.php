<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($data)
    {
        $attributes = $this->transformAttributes();

        $productArray = parent::toArray($data);
        $productArray['attributes'] = $attributes;
        unset($productArray['product_attribute_values']);
        return $productArray;
    }

    private function transformAttributes()
    {
        $attributes = [];

        foreach ($this->productAttributeValues as $productAttributeValue) {
            $attributeValue = $productAttributeValue->attributeValue;
            $attribute = $attributeValue->attribute;

            if (!isset($attributes[$attribute->id])) {
                $attributes[$attribute->id] = [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'translations' => $attribute->translations,
                    'values' => []
                ];
            }

            $attributes[$attribute->id]['values'][] = [
                'id' => $attributeValue->id,
                'name' => $attributeValue->name,
                'display_name' => $attributeValue->display_name,
                'color_code' => $attributeValue->color_code,
            ];
        }

        return array_values($attributes);
    }
}
?>