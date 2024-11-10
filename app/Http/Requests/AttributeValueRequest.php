<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        $rules = [

            'status' => 'required|boolean',

            'attribute_id' => [
                'required',
                'integer',
                'exists:attributes,id',
                'gt:0',
            ],

            'color_code' => [
                'nullable',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', // Hex color regex like #FF0000
            ],



            'sort_order' => [
                'integer',
                'min:0',
                'max:100', 
            ],

            'name' => [
                'required',
                'string',
                'min:1',
                'max:200',
                'unique:attribute_values,name,'.$id
                // Rule::unique('attribute_values', 'name')->ignore($id)
            ],
            'display_name' => [
                'nullable',
                'string',
                'min:2',
                'max:200',
                'unique:attribute_values,display_name,'.$id
                // Rule::unique('attribute_values', 'display_name')->ignore($id)
            ]

            // Translation arrays

            // 'name' => [
            //     'required',
            //     'array',
            //     'min:'.$lang_number,
            //     'max:'.$lang_number,
            // ],
            // 'display_name' => [
            //     'nullable',
            //     'array',
            //     'min:'.$lang_number,
            //     'max:'.$lang_number,
            // ],

        ];

        // // Add translation rules for each locale
        // if($lang_number > 0){ 
        //     foreach (config('translatable.locales.' . config('translatable.locale')) as $keyLang => $lang) {
        //         $rules["name.$keyLang"] = [
        //             'required',
        //             'string',
        //             'min:1',
        //             'max:200',
        //             Rule::unique('attribute_value_translations', 'name')
        //                 ->ignore($id, 'attribute_value_id')
        //                 ->where(function ($query) use ($keyLang) {
        //                     return $query->where('locale', $keyLang);
        //                 })
        //         ];
        //         $rules["display_name.$keyLang"] = [
        //             'nullable',
        //             'string',
        //             'min:2',
        //             'max:200',
        //             Rule::unique('attribute_value_translations', 'display_name')
        //                 ->ignore($id, 'attribute_value_id')
        //                 ->where(function ($query) use ($keyLang) {
        //                     return $query->where('locale', $keyLang);
        //                 })
        //         ];
        //     }
        // }
    
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
