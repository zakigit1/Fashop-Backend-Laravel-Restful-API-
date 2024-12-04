<?php

namespace App\Http\Requests;

use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SliderRequest extends FormRequest
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
        $id = $this->id;
        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes (programmer common use)
        $lang_number = count(config('translatable.locales.'.config('translatable.locale')));



        $rules = [
            // Image validation
            'image' => [
                $id ? 'nullable' : 'required',
                'image',
                'max:' . $maxFileSize,
                'mimes:jpeg,png,jpg,webp,svg', // Add supported formats
            ],

            'image_url' => [
                'nullable',
                'url',
                'max:255',
            ],

            'button_link' => [
                'nullable',
                'url',
                'max:255',
            ],

            'status' => 'required|boolean',

            'order' => 'required|integer|min:1|max:100|gt:0|unique:sliders,order,'.$id,


            // Translation arrays

            'title' => [
                'required',
                'array',
                'min:'.$lang_number,
                'max:'.$lang_number,
            ],
            'description' => [
                'required',
                'array',
                'min:'.$lang_number,
                'max:'.$lang_number,
            ],
        ];



        // Add translation rules for each locale
        if($lang_number > 0){ 
            foreach (config('translatable.locales.' . config('translatable.locale')) as $keyLang => $lang) {
                $rules["title.$keyLang"] = [
                    'required',
                    'string',
                    'min:2',
                    'max:200',
                    Rule::unique('slider_translations', 'title')
                        ->ignore($id, 'slider_id')
                        ->where(function ($query) use ($keyLang) {
                            return $query->where('locale', $keyLang);
                        })
                ];

                $rules["description.$keyLang"] = [
                    'required',
                    'string',
                    // Rule::unique('slider_translations', 'description')
                    //     ->ignore($id, 'slider_id')
                    //     ->where(function ($query) use ($keyLang) {
                    //         return $query->where('locale', $keyLang);
                    //     })
                ];

            }
        }

        return $rules;
    }

}