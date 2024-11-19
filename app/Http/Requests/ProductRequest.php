<?php

namespace App\Http\Requests;

use App\Models\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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



    public function rules()
    {
        $id = $this->id;

        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes (programmer common use)
        $maxPrice = 1000000; // 100 million
        $maxQty = 100000;
        $lang_number = count(config('translatable.locales.'.config('translatable.locale')));
        $attributeCount = Attribute::count();

     

        $rules = [
            // Image validation
            'thumb_image' => [
                $id ? 'nullable' : 'required',
                'image',
                'max:' . $maxFileSize,
                'mimes:jpeg,png,jpg,webp,svg', // Add supported formats
            ],


            // Inventory validation
            'qty' => [
                'required',
                'integer',
                'min:0',
                'max:' . $maxQty,
            ],

            // Price validation
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:' . $maxPrice,
                'decimal:0,2', // Allow up to 2 decimal places
            ],

            // Offer validation
            'offer_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:' . $maxPrice,
                'lt:price',
                'decimal:0,2',
                'required_with:offer_start_date,offer_end_date',
            ],

            'offer_start_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
                'required_with:offer_price',
            ],

            'offer_end_date' => [
                'nullable',
                'date',
                'after:offer_start_date',
                'required_with:offer_price',
            ],


            'status' => 'required|boolean',

            'video_link' => [
                'nullable',
                'url',
                'max:255',
            ],

            'brand_id' => [
                'nullable',
                'integer',
                'exists:brands,id',
            ],

            'product_type_id' => [
                'nullable',
                'integer',
                'exists:product_types,id',
            ],

            // Translation arrays

            'name' => [
                'required',
                'array',
                'min:'.$lang_number,
                'max:'.$lang_number,
            ],
            'description' => [
                'required',
                'array',
                'min:'.$lang_number,
                'max:'.$lang_number,
            ],

            'category_id' => 'required|numeric|integer|exists:categories,id|gt:0',// you can after add this column to products table because we store just one category for a product 

            'productAttributes' => [
                'required',
                'array',
                'min:1',
                'max:'.$attributeCount,
            ],

            'productAttributes.*.attribute_id' => [
                'required',
                'integer',
                'exists:attributes,id',
            ],

            'productAttributes.*.values' => [
                'required',
                'array',
                'min:1'
            ],

            'productAttributes.*.values.*.attribute_value_id' => [
                'required',
                'integer',
                'exists:attribute_values,id',
            ],


        ];

        // Add translation rules for each locale
        if($lang_number > 0){ 
            foreach (config('translatable.locales.' . config('translatable.locale')) as $keyLang => $lang) {
                $rules["name.$keyLang"] = [
                    'required',
                    'string',
                    'min:2',
                    'max:200',
                    Rule::unique('product_translations', 'name')
                        ->ignore($id, 'product_id')
                        ->where(function ($query) use ($keyLang) {
                            return $query->where('locale', $keyLang);
                        })
                ];

                $rules["description.$keyLang"] = [
                    'required',
                    'string',
                ];

            }
        }
    
        return $rules;
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
