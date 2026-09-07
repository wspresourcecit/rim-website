<?php

namespace App\Http\Requests\v1\Backend\Authorization;

use Illuminate\Foundation\Http\FormRequest;

class RoleManageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name,' . $this->route('role')?->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'is_active' => 'nullable|boolean',
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
