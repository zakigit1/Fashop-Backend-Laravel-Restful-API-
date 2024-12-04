<?php

namespace App\Http\Requests;

use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class PaypalSettingRequest extends FormRequest
{
    use ValidationTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'=>'required|boolean',
            'mode'=>['required','in:sandbox,live'],
            'country_name'=>'required|max:200',
            'currency_name'=>'required|max:200',
            'currency_rate'=>'required',
            'client_id'=>'required',
            'secret_key'=>'required',
        ];
    }
}
