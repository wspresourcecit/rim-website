<?php

namespace App\Http\Requests\v1\Backend\Gallery;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GalleryTypeRequest extends FormRequest
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
        $galleryTypeId = $this->route('gallery_type');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('gallery_types', 'name')->ignore($galleryTypeId),
            ],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Gallery type name is required',
            'name.unique' => 'This name already exists',
        ];
    }
}
