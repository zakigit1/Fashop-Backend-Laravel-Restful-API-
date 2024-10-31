<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class BrandRequest extends FormRequest
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
        // $id = $this->route('id');
        // $id = $this->id;
        $id = $this->id;
    
        ### Method 1 : 
        // return [
            // 'logo' => $id ? 'nullable|image' : 'required|image',
            // 'name' => [
            //     'required',
            //     'array',
            //     'min:3', // assuming you want at least 3 languages
            //     'max:3', // assuming you want at most 3 languages
            // ],
            // 'name.*' => 'required|string|max:200|unique:brand_translations,name,'.$id,
            // 'status' => 'required|boolean',
        // ];


        ### Method 2 : this is more Effective 
        // $rules = [
        //     'logo' => $id ? 'nullable|image' : 'required|image',
        //     'name' =>  $id ? 'array' : 'required|array',
        //     'status' =>  $id ? 'boolean' : 'required|boolean',
           
        // ];

        // // Add rules for each locale
        // foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { 
        //     $rules["name.$keyLang"] = $id ? 'string|min:2|max:100|unique:brand_translations,name,'.$id.',brand_id' : 'required|string|min:2|max:100|unique:brand_translations,name,'.$id.',brand_id';
        // }
        // return $rules;


        ### Method 3 : this is more Effective [For Ramy] perfect
        $rules = [
            'logo' => $id ? 'nullable|image' : 'required|image',
            'name' => 'required|array',
            'status' => 'required|boolean',
        
        ];

        // Add rules for each locale
        foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { 
            $rules["name.$keyLang"] = 'required|string|min:2|max:200|unique:brand_translations,name,'.$id.',brand_id';
        }
        return $rules;
    }


    // public function messages(){

    //     return [
    //         'logo.required' => 'The logo field is required.',
    //         'status.required' => 'The status field is required.',
    //         'status.boolean' => 'The status must be true or false.',
    //     ];
    // }

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
