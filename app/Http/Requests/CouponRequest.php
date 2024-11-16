<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
        $id = $this->coupon;
        $maxQty = 10000;

        
        return [

            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('coupons', 'name')->ignore($id),
            ],

            'code' => [
                'required',
                'string',
                'max:200',
                Rule::unique('coupons', 'code')->ignore($id),
            ],

            'quantity' => 'required|numeric|integer|min:0|max:'.$maxQty ,

            'max_use' => 'required|numeric|integer|min:1' ,

            'start_date' => 'required|date|after_or_equal:today' ,

            'end_date' => 'required|date|after:start_date' ,

            'discount_type' => 'required|in:amount,percentage' ,

            'discount' => 'required|numeric' ,

            'status' => 'required|boolean' ,
        ];
    }
}
