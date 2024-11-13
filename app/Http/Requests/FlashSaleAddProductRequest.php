<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlashSaleAddProductRequest extends FormRequest
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
        return [
            'product_id' => [
                'required',
                'exists:products,id',
                'numeric',
                'integer',
                'unique:flash_sale_items,product_id',
                'gt:0'
            ],
            'show_at_home'=>'required|boolean',
            'status'=>'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'The selected product is not found.',
            'product_id.unique' => 'Product is already added to the flash sale.',
        ];
    }
}
