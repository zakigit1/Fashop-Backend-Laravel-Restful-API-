<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRuleRequest extends FormRequest
{
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

        $id = $this->shipping_rule;

        return [
            'name' => 'required||unique:shipping_rules,name,' . $id,
            'type' => 'required|string', // Ensure this is one of the predefined types, consider using `in:...`
            // 'type' => [
            //     'required',
            //     'string',
            //     Rule::in(['type1', 'type2', 'type3']), // Define allowed types
            // ],
            'min_cost' => 'nullable|numeric|decimal:0,2',
            'max_cost' => 'nullable|numeric|decimal:0,2',
            'cost' => 'required|numeric|decimal:0,2',
            'weight_limit' => 'nullable|numeric|decimal:0,2',
            'description' => 'nullable|string',
            'region' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:100',
            'delivery_time' => 'nullable|string|max:50',
            'status' => 'required|boolean'
    
    ];
}
}
