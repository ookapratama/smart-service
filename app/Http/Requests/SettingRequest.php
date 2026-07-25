<?php

namespace App\Http\Requests;

class SettingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:800',
            'app_favicon' => 'nullable|file|mimes:ico,png|max:800',
            'app_name' => 'nullable|string|max:255',
            'app_description' => 'nullable|string|max:255',
            'app_keywords' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'social_instagram' => 'nullable|string|max:255',
            'theme_color' => 'nullable|string|max:255',
            'maintenance_mode' => 'nullable|in:0,1',
            'allow_registration' => 'nullable|in:0,1',
        ];
    }
}
