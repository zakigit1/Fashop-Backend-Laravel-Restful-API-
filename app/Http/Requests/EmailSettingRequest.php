<?php

namespace App\Http\Requests;

use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class EmailSettingRequest extends FormRequest
{
    use ValidationTrait;
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
            'name'=>'required|string|max:100',
            'email'=>'required|email|max:200',
            'host'=>'required|max:200',
            'username'=>'required|max:200',
            'password'=>'required|max:200',
            'port'=>'required|max:200',
            'encryption'=>'required|in:tls,ssl',
        ];
    }

    public function messages(){
        return [
            'encryption.in'=>'Please select the encryption between tls and ssl',
        ];
    }
}
