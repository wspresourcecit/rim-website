<?php

namespace App\Http\Requests\v1\Backend\Setting;

use App\Rules\Base64ImageValidation;
use Illuminate\Foundation\Http\FormRequest;

class WebsiteAssetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $part = $this->query("part");
        $rules = [];
        if ($part == "info") {
            $rules = [
                'footer_text' => 'nullable|string|max:255',
                'e_tin' => 'nullable|string|max:255',
                'trade_license' => 'nullable|string',
                'copyright_text' => 'nullable|string',
                'timezone' => 'nullable|string',
            ];
        } elseif ($part == "contact") {
            $rules = [
                'number' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'map_title' => 'nullable|string',
                'map' => 'nullable|string|max:2000',
            ];
        } elseif ($part == "asset") {
            $rules = [
                'favicon' => ['nullable', new Base64ImageValidation],
                'main_logo' => ['nullable', new Base64ImageValidation],
                'dark_logo' => ['nullable', new Base64ImageValidation],
            ];
        } elseif ($part == "social-media") {
            $rules = [
                'social_handles' => 'nullable|array',
                'social_handles.*.platform_icon' => 'nullable|string',
                'social_handles.*.link' => 'nullable|url',
                'fb_page_follower' => 'nullable|string|max:255',
                'youtube_subscriber' => 'nullable|string|max:255',
                'fb_group_follower' => 'nullable|string|max:255',

                'floating_facebook_link' => 'nullable',
                'floating_instagram_link' => 'nullable',
                'floating_whatsapp_number' => 'nullable',
            ];
        } elseif ($part == "maintenance") {
            $rules = [
                'maintenance_mode' => 'required|boolean',
                'maintenance_message' => 'required_if:maintenance_mode,true|nullable|string|max:65535',
            ];
        }

        return $rules;
    }


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
