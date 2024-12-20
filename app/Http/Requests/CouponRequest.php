<?php

namespace App\Http\Requests;

use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
        $id = $this->coupon;
        $maxQty = 10000;
        $maxExtraPrice = 1000000; // 100 million

        
        return [

            'name' => [
                'required',
                'string',
                'max:200',
                // Rule::unique('coupons', 'name')->ignore($id),
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

            'min_purchase_amount' => [
                'numeric',
                'min:0',
                'max:' . $maxExtraPrice,
                'regex:/^\d+(\.\d{1,2})?$/', // Ensures exactly 2 decimal places
            ],
        ];
    }
}
