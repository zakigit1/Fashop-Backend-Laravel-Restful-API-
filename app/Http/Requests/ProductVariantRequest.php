<?php

namespace App\Http\Requests;


use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;


class ProductVariantRequest extends FormRequest
{

    use ValidationTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $productId = $this->id;
        $id = $this->variantId;

        // dd($productId);
        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes (programmer common use)
        $maxExtraPrice = 1000000; // 100 million


        $rules = [
            // Barcode image validation
            'barcode' => [
                'nullable',
                'image',
                'max:' . $maxFileSize,
                'mimes:jpeg,png,jpg,webp,svg', // Add supported 
                function ($image, $value, $fail)  {
                    if (is_array($value)) {
                        $fail('Only a single image can be uploaded.');
                    }
                },
            ],


             // Product ID validation
            // 'product_id' => [
            //     $id ? 'nullable' : 'required',
            //     'integer',
            //     'exists:products,id',
            // ],


            // Extra price validation
            'extra_price' => [
                'numeric',
                'min:0',
                'max:' . $maxExtraPrice,
                'regex:/^\d+(\.\d{1,2})?$/', // Ensures exactly 2 decimal places
            ],
            
            
            // Quantity validation
            'quantity' => [
                'required',
                'numeric',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($productId, $id) {
                    $product = Product::where('id', $productId)->select('id', 'variant_quantity')->first();
                    $variant = ProductVariant::find($id);

                    $availableQuantity = $product->variant_quantity + ($variant ? $variant->quantity : 0);

                    if ($value > $availableQuantity) {
                        $fail('Quantity cannot exceed available variant quantity.');
                    }
                },
            ],


            // SKU validation
            'sku' => [
                'nullable',
                'string',
                'min:10',
                'max:50',
                Rule::unique('product_variants', 'sku')->ignore($id), // Add unique constraint
            ],
            

            // In stock validation
            'in_stock' => 'boolean',


            // Attribute values validation
            'attribute_values' => [
                'required',
                'array',
                'min:1',
                /** this method more optimazable and more professional :*/
                // function ($attribute, $values, $fail) use ($productId, $id) {
                //     $id = $id ?? null; 

                //     $attributeValues = AttributeValue::whereIn('id', $values)
                //         ->select('id', 'attribute_id')
                //         ->get()
                //         ->pluck('attribute_id')
                //         ->toArray();
                
                //     if (count(array_unique($values)) !== count($values)) {
                //         $fail('Duplicate attribute values are not allowed. Please enter a unique value for each attribute.');
                //     }
                
                //     if (count(array_unique($attributeValues)) !== count($attributeValues)) {
                //         $fail('Duplicate attributes are not allowed. Please enter a unique attribute.');
                //     }


                
                //     $variantHash = ProductVariant::generateVariantHash($values);
                
                //     $variantExists = ProductVariant::where('product_id', $productId)
                //         ->where('variant_hash', $variantHash)
                //         ->where(function ($query) use ($id) {
                //             if ($id) {
                //                 $query->where('id', '!=', $id);
                //             }
                //         })
                //         ->exists();
                
                //     if ($variantExists) {
                //         $fail('Product variant attribute values are already exists for this product');
                //     }
                // },


                // function ($attribute_value, $values, $fail) use ($productId, $id) {
                //     $id = $id ?? null;
            
                //     // Check if attribute values exist and are valid
                //     $productAttributeValues = ProductAttributeValue::where('product_id', $productId)
                //         ->whereIn('attribute_value_id', $values)
                //         ->pluck('attribute_value_id')
                //         ->toArray();

                //     $productAttribute = ProductAttributeValue::where('product_id', $productId)
                //         ->whereIn('attribute_value_id', $values)
                //         ->pluck('attribute_id')
                //         ->toArray();
                            

                
                //     if (count(array_unique($productAttribute)) !== count($productAttribute)) {
                //         $fail('Duplicate attributes are not allowed. Please enter a unique attribute.');
                //     }

                   
                //     if (count(array_unique($values)) !== count($values)) {
                //         $fail('Duplicate attribute values are not allowed. Please enter a unique value for each attribute.');
                //     }

                //     // Check if attribute values are specific to this product
                //     if (count($productAttributeValues) !== count($values)) {
                //         if (array_diff($values, $productAttributeValues)) {
                //             $fail('Attribute values are not specific to this product. Please select valid attributes.');
                //         }
                //     }
            
                    

                //     // Generate variant hash and check if variant exists
                //     $variantHash = ProductVariant::generateVariantHash($values);
            
                //     $variantExists = ProductVariant::where('product_id', $productId)
                //         ->where('variant_hash', $variantHash)
                //         ->where(function ($query) use ($id) {
                //             if ($id) {
                //                 $query->where('id', '!=', $id);
                //             }
                //         })
                //         ->exists();
            
                //     if ($variantExists) {
                //         $fail('Product variant attribute values are already exists for this product');
                //     }
                // },
            


                function ($attribute_value, $values, $fail) use ($productId, $id) {
                    $id = $id ?? null;
                
                    // Retrieve product attribute values and attributes in a single query
                    $productAttributeValues = ProductAttributeValue::where('product_id', $productId)
                        ->whereIn('attribute_value_id', $values)
                        ->select('attribute_value_id', 'attribute_id')
                        ->get()
                        ->toArray();
                
                    // Check for duplicates in attribute values and attributes
                    $attributeValues = array_column($productAttributeValues, 'attribute_value_id');
                    $attributes = array_column($productAttributeValues, 'attribute_id');
                
                    if (count(array_unique($attributes)) !== count($attributes)) {
                        $fail('Duplicate attributes are not allowed. Please enter a unique attribute.');
                    }
                
                    if (count(array_unique($values)) !== count($values)) {
                        $fail('Duplicate attribute values are not allowed. Please enter a unique value for each attribute.');
                    }
                
                    // Check if attribute values are specific to this product
                    if (count($attributeValues) !== count($values)) {
                        if (array_diff($values, $attributeValues)) {
                            $fail('Attribute values are not specific to this product. Please select valid attributes.');
                        }
                    }
                
                    // Generate variant hash and check if variant exists
                    $variantHash = ProductVariant::generateVariantHash($values);
                
                    $variantExists = ProductVariant::where('product_id', $productId)
                        ->where('variant_hash', $variantHash)
                        ->when($id, function ($query) use ($id) {
                            $query->where('id', '!=', $id);
                        })
                        ->exists();
                
                    if ($variantExists) {
                        $fail('Product variant attribute values are already exists for this product');
                    }
                }


                /** this method more optimazable */
                // function ($attribute, $values, $fail)use($productId) {
                //     $attributeIds = AttributeValue::whereIn('id', $values)
                //         ->pluck('attribute_id')
                //         ->toArray();
                
                //     if (count(array_unique($values)) !== count($values)) {
                //         $fail('Duplicate attribute values are not allowed. Please enter a unique value for each attribute.');
                //     }
                //     if (count(array_unique($attributeIds)) !== count($attributeIds)) {
                //         $fail('Duplicate attributes are not allowed. Please enter a unique attribute.');
                //     }

                //     $variantHash = ProductVariant::generateVariantHash($values);

                //     $variantExists = ProductVariant::where('product_id', $productId)
                //     ->where('variant_hash', $variantHash)
                //     ->exists();
                //     if($variantExists){
                //         return $this->error('Product variant attribute values are already exists for this product');
                //     }
                // },
                /** this method not optimazable */
                // function ($attribute, $values, $fail) {
                //     $attributeIds = [];
                //     foreach($values as $value){
                //         $attribute_value = AttributeValue::where('id', $value)->select('id','attribute_id')->first();
                //         $attributeIds[] = $attribute_value->attribute_id;
                //     }
                //     if (count(array_unique($attributeIds)) !== count($attributeIds)) {
                //         $fail('Duplicate attributes are not allowed. Please enter a unique value for each attribute.');
                //     }
                // },
            ],

            'attribute_values.*' => 'required|numeric|integer|exists:product_attribute_values,attribute_value_id|gt:0', 
            // 'attribute_values.*' => ['required', 'numeric', 'integer', Rule::exists('product_attribute_values', 'attribute_value_id')->where('attribute_value_id', '>', 0)],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'attribute_values.*.required' => 'Please select at least one attribute value.',
            'attribute_values.*.numeric' => 'Attribute value must be a number.',
            'attribute_values.*.integer' => 'Attribute value must be an integer.',
            'attribute_values.*.exists' => 'You don\'t have this attribute value for this product.',
            'attribute_values.*.gt' => 'Attribute value must be greater than 0.',
            'attribute_values.*.unique' => 'Duplicate attribute values are not allowed. Please enter a unique value for each attribute.',
            'attribute_values.*.distinct' => 'Duplicate attributes are not allowed. Please enter a unique attribute.',
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'Product ID does not exist.',
            'variant_hash.required' => 'Variant hash is required.',
            'variant_hash.exists' => 'Variant hash does not exist.',
            'variant_hash.unique' => 'Product variant attribute values are already exists for this product.',
        ];
    }





}
