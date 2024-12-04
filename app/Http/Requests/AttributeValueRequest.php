<?php

namespace App\Http\Requests;

// use App\Models\Attribute;
// use App\Models\AttributeTranslation;
use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Validation\Rule;

class AttributeValueRequest extends FormRequest
{
    use ValidationTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        $id = $this->id;
        $attributeId = $this->attribute_id;

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

                /** need to fix this code  */
                // function ($attribute, $value, $fail) {
                //     if ($this->attribute_id == 'color' && empty($value)) {
                //         $fail('Color code is required when attribute is color.');
                //     } elseif ($this->attribute_id != 'color' && !empty($value)) {
                //         $fail('Color code is not allowed when attribute is not color.');
                //     }
                // },

                // function ($attribute, $value, $fail) use ($attributeId) {
                //     if ($value) {
                //         $attribute = AttributeTranslation::where('attribute_id', $attributeId)
                //             ->where('name','!=','color')
                //             ->orWhere('name','!=','couleur')
                //             ->orWhere('name','!=','لون')
                //             ->exists();
                //         if (!$attribute) {
                //             $fail('Color code is not allowed when attribute is not color.');
                //         }
                //     }
                // },

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

}
