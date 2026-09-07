<?php

namespace App\Http\Requests\v1\Backend;

use App\Rules\Base64ImageValidation;
use Illuminate\Foundation\Http\FormRequest;

class PageHeaderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'page_key' => 'required|string',
            'heading' => 'nullable|string|max:255',
            'sub_heading' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_og_image' => ['nullable', new Base64ImageValidation],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
