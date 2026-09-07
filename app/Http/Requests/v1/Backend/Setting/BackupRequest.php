<?php

namespace App\Http\Requests\v1\Backend\Setting;

use Illuminate\Foundation\Http\FormRequest;

class BackupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'is_auto_backup' => 'required|boolean',
            'backup_frequency' => $this->input('is_auto_backup') ? 'required|in:daily,weekly,monthly' : 'nullable',
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
