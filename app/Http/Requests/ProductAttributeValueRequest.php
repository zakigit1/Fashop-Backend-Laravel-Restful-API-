<?php

namespace App\Http\Requests;

use App\Models\ProductAttributeValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductAttributeValueRequest extends FormRequest
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
    // public function rules(): array
    // {
    //     $id = $this->id;
    //     $maxPrice = 1000000; // 100 million
    //     $maxQty = 100000;
    //     $rules = [

    //         'attribute_id' => 'required|integer|exists:attributes,id|gt:0|required_with:attribute_value_id',
            
    //         'attribute_value_id' => 'required|integer|exists:attribute_values,id|gt:0|required_with:attribute_id',


    //         'extra_price' => [
    //             'numeric',
    //             'min:0',
    //             'max:' . $maxPrice,
    //             'decimal:0,2', // Allow up to 2 decimal places
    //         ],

    //         'quantity' => [
    //             'integer',
    //             'min:0',
    //             'max:' . $maxQty,
    //         ],
            
    //         'is_default' => [
    //             'boolean',
    //         ],


    //     ];



    //     return $rules;
    // }

    public function rules(): array
    {
        $id = $this->id;
        $productId = $this->route('productId'); // Get product_id from route parameter
        $maxPrice = config('products.max_price', 1000000);
        $maxQty = config('products.max_quantity', 100000);

        return [
            'attribute_id' => [
                'required',
                'integer',
                'exists:attributes,id',
                Rule::unique('product_attribute_values')
                    ->ignore($id)
                    ->where(function ($query) use ($productId) {
                        return $query->where('product_id', $productId)
                                   ->where('attribute_id', $this->attribute_id)
                                   ->where('attribute_value_id', $this->attribute_value_id);
                    }),
            ],
            
            'attribute_value_id' => [
                'required',
                'integer',
                'exists:attribute_values,id',
                Rule::unique('product_attribute_values')
                ->ignore($id)
                ->where(function ($query) use ($productId) {
                    return $query->where('product_id', $productId)
                               ->where('attribute_id', $this->attribute_id)
                               ->where('attribute_value_id', $this->attribute_value_id);
                }),
            ],

            'extra_price' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:' . $maxPrice,
                'regex:/^\d+(\.\d{1,2})?$/', // Ensures exactly 2 decimal places
            ],

            'quantity' => [
                'sometimes',
                'integer',
                'min:0',
                'max:' . $maxQty,
            ],
            
            'is_default' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) use ($productId) {
                    if ($value) {
                        $existingDefault = ProductAttributeValue::where('product_id', $productId)
                            ->where('is_default', true)
                            ->exists();
                        if ($existingDefault) {
                            $fail('Only one default value is allowed per product.');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attribute_id.unique' => 'This combination of product, attribute, and value already exists.',
            'attribute_id.exists' => 'The selected attribute is invalid.',
            'attribute_value_id.unique' => 'This combination of product, attribute, and value already exists.',
            'attribute_value_id.exists' => 'The selected attribute value is invalid.',
            'extra_price.regex' => 'The price must have no more than 2 decimal places.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'extra_price' => $this->extra_price ?? 0.00,
            'quantity' => $this->quantity ?? 0,
            'is_default' => $this->is_default ?? false,
        ]);
    }

}
