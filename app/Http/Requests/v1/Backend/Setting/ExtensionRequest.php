<?php

namespace App\Http\Requests\v1\Backend\Setting;

use App\Rules\Base64ImageValidation;
use Illuminate\Foundation\Http\FormRequest;

class ExtensionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'short_code' => 'required|string|max:255|unique:extensions,short_code,' . $this->route('extension')?->id,
            'image' => $this->route('extension') ? ['nullable', new Base64ImageValidation()] : ['required', new Base64ImageValidation()],
            'image_width' => 'nullable|numeric',
            'image_height' => 'nullable|numeric',
            'instructions' => 'nullable|string|max:500',
            'credentials' => 'required|array',
            'credentials.*.key' => 'required_with:credentials.value|string|max:255',
            'credentials.*.value' => 'required_with:credentials.key|string|max:1600',
            'is_api' => 'required|boolean',
            'is_active' => 'required|boolean'
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
