<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
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

        $lang_number = count(config('translatable.locales.'.config('translatable.locale')));

        $rules = [

            'status' => 'required|boolean',

            'type' => [
                'string',
                'min:2',
                'max:255'
            ],
            
            'is_required' => [
                'boolean',
            ],

            'is_filterable' => [
                'boolean',
            ],

           
            'sort_order' => [
                'integer',
                'min:0',
                'max:100', 
            ],

            // Translation arrays

            'name' => [
                'required',
                'array',
                'min:'.$lang_number,
                'max:'.$lang_number,
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
                    Rule::unique('attribute_translations', 'name')
                        ->ignore($id, 'attribute_id')
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