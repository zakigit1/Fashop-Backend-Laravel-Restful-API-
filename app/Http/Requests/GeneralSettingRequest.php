<?php

namespace App\Http\Requests;

use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingRequest extends FormRequest
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
        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes (programmer common use)
        return [
            'site_name' => [
                'required',
                'string',
                'min:2',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_.]+$/u' // Allow letters, numbers, spaces, hyphens, underscores, dots
            ],
            'layout' => [
                'required',
                'string',
                'in:rtl,ltr' // Only allow specific layout values
            ],
            'contact_email' => [
                'required',
                // 'email:rfc,dns', // Strict email validation including DNS check
                'email',
                'max:200'
            ],
            'contact_phone' => [
                'nullable',
                'string',
                'regex:/^([+]?[\s0-9]+)?(\d{3}|[(]?[0-9]+[)])?([-]?[\s]?[0-9])+$/', // International phone format
                'min:10',
                'max:20'
            ],
            'contact_address' => [
                'nullable',
                'string',
                'max:500'
            ],
            'contact_map' => [
                'nullable',
                'url',
                'max:1000',
                'regex:/^https:\/\/(www\.)?(google\.com\/maps|maps\.google\.com)/' // Only allow Google Maps URLs
            ],
            'currency_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z]{3}$/' // Three-letter currency code (e.g., USD, EUR)
            ],
            'currency_icon' => [
                'required',
                'string',
                'max:10',
                'regex:/^[£$€¥₹₽﷼₪₱₩₫₴₦]|[A-Z]{2,3}$/' // Common currency symbols or codes
            ],
            'time_zone' => [
                'required',
                'string',
                'max:100',
                'timezone' // Built-in timezone validation
            ],
            'logo'=>[
                'nullable',
                'image',
                'max:' . $maxFileSize,
                'mimes:jpeg,png,jpg,webp,svg', // Add supported formats
            ],
            'favicon'=>[
                'nullable',
                'image',
                'max:' . $maxFileSize,
                'mimes:jpeg,png,jpg,webp,svg', // Add supported formats
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'site_name.regex' => 'The site name may only contain letters, numbers, spaces, and basic punctuation.',
            'layout.in' => 'The layout must be either RTL or LTR.',
            'contact_email.email' => 'Please provide a valid email address.',
            'contact_phone.regex' => 'Please provide a valid international phone number.',
            'contact_map.regex' => 'The map URL must be a valid Google Maps link.',
            'currency_name.regex' => 'The currency name must be a valid 3-letter currency code (e.g., USD).',
            'currency_icon.regex' => 'Please provide a valid currency symbol or code.',
            'time_zone.timezone' => 'Please provide a valid timezone.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'site_name' => 'website name',
            'layout' => 'website layout',
            'contact_email' => 'contact email',
            'contact_phone' => 'contact phone number',
            'contact_address' => 'contact address',
            'contact_map' => 'Google Maps link',
            'currency_name' => 'currency code',
            'currency_icon' => 'currency symbol',
            'time_zone' => 'timezone',
        ];
    }
}
