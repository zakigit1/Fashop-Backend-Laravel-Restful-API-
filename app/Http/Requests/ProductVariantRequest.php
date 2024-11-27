<?php

namespace App\Http\Requests;

use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;


class ProductVariantRequest extends FormRequest
{
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
                'mimes:jpeg,png,jpg,webp,svg', // Add supported formats
            ],


             // Product ID validation
            'product_id' => [
                $id ? 'nullable' : 'required',
                'integer',
                'exists:products,id',
            ],


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
                function ($attribute, $values, $fail) use ($productId, $id) {
                    $id = $id ?? null; 

                    $attributeValues = AttributeValue::whereIn('id', $values)
                        ->select('id', 'attribute_id')
                        ->get()
                        ->pluck('attribute_id')
                        ->toArray();
                
                    if (count(array_unique($values)) !== count($values)) {
                        $fail('Duplicate attribute values are not allowed. Please enter a unique value for each attribute.');
                    }
                
                    if (count(array_unique($attributeValues)) !== count($attributeValues)) {
                        $fail('Duplicate attributes are not allowed. Please enter a unique attribute.');
                    }
                
                    $variantHash = ProductVariant::generateVariantHash($values);
                
                    $variantExists = ProductVariant::where('product_id', $productId)
                        ->where('variant_hash', $variantHash)
                        ->where(function ($query) use ($id) {
                            if ($id) {
                                $query->where('id', '!=', $id);
                            }
                        })
                        ->exists();
                
                    if ($variantExists) {
                        $fail('Product variant attribute values are already exists for this product');
                    }
                },


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

            'attribute_values.*' => 'required|numeric|integer|exists:product_attribute_values,id|gt:0', 
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




    /**
     * Summary of failedValidation : this function for return error validation in the response 
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    protected function failedValidation(Validator $validator):JsonResponse
    {
        throw new HttpResponseException(            
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
