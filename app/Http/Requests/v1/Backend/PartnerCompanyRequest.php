<?php

namespace App\Http\Requests\v1\Backend;

use App\Models\PartnerCompany;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerCompanyRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $imageRules = ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'];

        if ($this->isMethod('POST')) {
            return [
                'type' => ['required', Rule::in(PartnerCompany::TYPES)],
                'logos' => ['required', 'array', 'max:12'],
                'logos.*' => ['required', 'string', 'starts_with:data:image/'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'type' => ['sometimes', Rule::in(PartnerCompany::TYPES)],
            'logo' => array_merge(['nullable'], $imageRules),
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Type is required',
            'type.in' => 'Selected type is invalid',
            'logos.required' => 'At least one logo is required',
            'logos.max' => 'You can upload a maximum of 12 logos at a time',
            'logos.*.required' => 'Logo is required',
            'logos.*.string' => 'Each logo must be a valid image',
            'logos.*.starts_with' => 'Each logo must be a valid image',
        ];
    }
}
