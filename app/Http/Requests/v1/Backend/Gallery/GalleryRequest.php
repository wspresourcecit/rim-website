<?php

namespace App\Http\Requests\v1\Backend\Gallery;

use App\Rules\Base64ImageValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
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
        return [
            'gallery_type_id' => ['required', 'exists:gallery_types,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'image' => [$this->isMethod('POST') ? 'required' : 'nullable', new Base64ImageValidation],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'gallery_type_id.required' => 'Gallery type is required',
            'gallery_type_id.exists' => 'Selected gallery type does not exist',
            'image.required' => 'Image is required',
        ];
    }
}
