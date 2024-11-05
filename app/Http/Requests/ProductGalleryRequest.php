<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductGalleryRequest extends FormRequest
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
            'image' =>['required','array','min:1','max:10'],
            'image.*'=>['required','image','mimes:jpg,jpeg,png','max:80640'],
            'product_id'=>['required','numeric','exists:products,id','gt:0']
        ];
    }
}
