<?php

namespace App\Http\Requests;

class SettingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',
            'app_favicon' => 'nullable|file|mimes:ico,png,jpg,svg|max:800',
            'app_name' => 'nullable|string|max:255',
            'app_description' => 'nullable|string|max:500',

            'hero_bg' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'hero_badge' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'hero_btn1_text' => 'nullable|string|max:255',
            'hero_btn2_text' => 'nullable|string|max:255',

            'service_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'service_badge' => 'nullable|string|max:255',
            'service_title' => 'nullable|string|max:255',
            'service_subtitle' => 'nullable|string|max:1000',

            'contact_phone' => 'nullable|string|max:255',
            'social_instagram' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|string|max:255',
            'social_youtube' => 'nullable|string|max:255',

            'profile_kecamatan' => 'nullable|string|max:255',
            'profile_kota' => 'nullable|string|max:255',
            'profile_alamat' => 'nullable|string|max:500',
            'profile_telepon' => 'nullable|string|max:255',
            'profile_email' => 'nullable|string|max:255',
            'profile_visi' => 'nullable|string|max:1000',
            'profile_deskripsi_lengkap' => 'nullable|string|max:2000',

            'maintenance_mode' => 'nullable|in:0,1',
            'allow_registration' => 'nullable|in:0,1',

            'wa_driver' => 'nullable|in:log,fonnte',
            'wa_gateway_url' => 'nullable|string|max:255',
            'wa_gateway_token' => 'nullable|string|max:255',
            'tiket_prefix' => 'nullable|string|max:50',

            'ttd_jabatan' => 'nullable|string|max:255',
            'ttd_nama' => 'nullable|string|max:255',
            'ttd_nip' => 'nullable|string|max:50',
        ];
    }
}
