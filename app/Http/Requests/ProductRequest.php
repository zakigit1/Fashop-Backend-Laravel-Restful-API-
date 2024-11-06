<?php

namespace App\Http\Requests;

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

    // public function rules(): array
    // {
    //     $id = $this->id;
        
        // ### Method 2 : this is more Effective [testing with post man]
        // $rules = [

        // 'thumb_image' => $id ? 'nullable' : 'required',

        // 'qty'=> $id ? 'numeric|integer|min:0|max:100000' : 'required|numeric|integer|min:0|max:100000',
        // 'price'=> $id ? 'numeric|min:0|max:100000000' : 'required|numeric|min:0|max:100000000',
        // 'offer_price'=> $id ? 'numeric|min:0|max:100000000' : 'required|numeric|min:0|max:100000000',
        // 'status' => $id ? 'boolean' : 'required|boolean',

        // 'offer_start_date'=> 'nullable|date|after_or_equal:today',
        // 'offer_end_date'=> 'nullable|date|after:offer_start_date',
        // 'sku'=> 'nullable|string|min:10|max:50',
        // 'video_link'=> 'nullable|url',

        // 'brand_id' => 'nullable|numeric|exists:brands,id',
        
        // 'name' =>  $id ? 'array' : 'required|array',
        // 'description' => $id ? 'array' : 'required|array',
        // 'product_type' => 'nullable|array',

        // ];

        // // Add rules for each locale
        // foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { 
        //     $rules["name.$keyLang"] = $id ? 'string|min:2|max:100|unique:category_translations,name,'.$id.',category_id' : 'required|string|min:2|max:100|unique:category_translations,name,'.$id.',category_id';
        //     $rules["name.$keyLang"] = $id ? 'string|min:2|max:100|unique:category_translations,name,'.$id.',category_id' : 'required|string|min:2|max:100|unique:category_translations,name,'.$id.',category_id';
        //     $rules["name.$keyLang"] = $id ? 'string|min:2|max:100|unique:category_translations,name,'.$id.',category_id' : 'required|string|min:2|max:100|unique:category_translations,name,'.$id.',category_id';
        // }
        // return $rules;


    //     ### Method 2 : this is more Effective [For Ramy] perfect
    //     $rules = [
    //         'thumb_image' => $id ? 'nullable|image|max:5000' : 'required|image|max:5000',

    //         'qty'=> 'required|numeric|integer|min:0|max:100000',
    //         'price'=> 'required|numeric|min:0|max:100000000|gt:offer_price',// if price give you issue remove gt:offer_price
    //         'status' => 'required|boolean',
            
    //         'offer_price'=> 'nullable|numeric|min:0|max:100000000|lt:price',
    //         'offer_start_date'=> 'nullable|date|after_or_equal:today',
    //         'offer_end_date'=> 'nullable|date|after:offer_start_date',
    //         'sku'=> 'nullable|string|min:10|max:50',
    //         'video_link'=> 'nullable|url',

    //         'brand_id' => 'nullable|numeric|exists:brands,id',
            
    //         'name' => 'required|array',
    //         'description' => 'required|array',
    //         'product_type' => 'nullable|array',
    //     ];

        // // Add rules for each locale
    //     foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { 
    //         $rules["name.$keyLang"] = 'required|string|min:2|max:200|unique:product_translations,name,'.$id.',product_id';
    //         $rules["description.$keyLang"] = 'required|string|unique:product_translations,description,'.$id.',product_id';
    //         $rules["product_type.$keyLang"] = 'string|min:2|max:100|unique:product_translations,product_type,'.$id.',product_id';
    //     }
    //     return $rules;
    // }


    ### Method 2 : this is Perfectooooo [For Ramy] 
    public function rules()
    {
        $id = $this->id;

        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes (programmer common use)
        $maxPrice = 1000000; // 100 million
        $maxQty = 100000;
        $lang_number = count(config('translatable.locales.'.config('translatable.locale')));



        $rules = [
            // Image validation
            'thumb_image' => [
                $id ? 'nullable' : 'required',
                'image',
                'max:' . $maxFileSize,
                'mimes:jpeg,png,jpg,webp', // Add supported formats
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

            // Product details
            'sku' => [
                'nullable',
                'string',
                'min:10',
                'max:50',
                Rule::unique('products', 'sku')->ignore($id), // Add unique constraint
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
                'required',
                'integer',
                'exists:product_types,id',
                'gt:0',
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

            'category_ids' => 'required|array|min:1', //[]
            'category_ids.*' => 'numeric|exists:categories,id|gt:0',

            'attribute_ids' => 'required|array|min:1', //[]
            'attribute_ids.*' => 'numeric|exists:attributes,id|gt:0',
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
                    Rule::unique('product_translations', 'description')
                        ->ignore($id, 'product_id')
                        ->where(function ($query) use ($keyLang) {
                            return $query->where('locale', $keyLang);
                        })
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
