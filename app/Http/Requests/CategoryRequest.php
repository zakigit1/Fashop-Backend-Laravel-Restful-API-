<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CategoryRequest extends FormRequest
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
        $id = $this->id;
        
        // ### Method 2 : this is more Effective [testing with post man]
        // $rules = [
        //     'icon' => $id ? 'string' : 'required|string',
        //     'name' =>  $id ? 'array' : 'required|array',
        //     'status' =>  $id ? 'boolean' : 'required|boolean',
        //     'parent_id' => 'nullable|numeric|exists:categories,id'
        // ];

        // // Add rules for each locale
        // foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { 
        //     $rules["name.$keyLang"] = $id ? 'string|min:2|max:100|unique:category_translations,name,'.$id.',category_id' : 'required|string|min:2|max:100|unique:category_translations,name,'.$id.',category_id';
        // }
        // return $rules;


        ### Method 3 : this is more Effective [For Ramy] perfect
        $rules = [
            'icon' => 'required|string',
            'name' => 'required|array',
            'status' => 'required|boolean',
            'parent_id' => 'nullable|numeric|exists:categories,id'
        
        ];

        // Add rules for each locale
        foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { 
            $rules["name.$keyLang"] = 'required|string|min:2|max:100|unique:category_translations,name,'.$id.',category_id';
        }
        return $rules;
        
    }


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
