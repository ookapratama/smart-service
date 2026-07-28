<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            [
                'key' => 'app_name',
                'value' => 'Soreang Smart Service (3S)',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Nama Aplikasi',
            ],
            [
                'key' => 'app_description',
                'value' => 'Platform pelayanan publik terpadu Kecamatan Soreang, Kota Parepare.',
                'group' => 'general',
                'type' => 'textarea',
                'label' => 'Deskripsi Aplikasi',
            ],
            [
                'key' => 'app_keywords',
                'value' => 'laravel, base template, premium dashboard',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Kata Kunci SEO',
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'group' => 'general',
                'type' => 'image',
                'label' => 'Logo Aplikasi',
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'group' => 'general',
                'type' => 'image',
                'label' => 'Favicon',
            ],

            // Contact & Social
            [
                'key' => 'contact_email',
                'value' => 'admin@ooka.id',
                'group' => 'contact',
                'type' => 'text',
                'label' => 'Email Kontak',
            ],
            [
                'key' => 'contact_phone',
                'value' => '08123456789',
                'group' => 'contact',
                'type' => 'text',
                'label' => 'Nomor WhatsApp',
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/ookapratama',
                'group' => 'contact',
                'type' => 'text',
                'label' => 'URL Instagram',
            ],

            // System
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'group' => 'system',
                'type' => 'boolean',
                'label' => 'Mode Perawatan',
            ],
            [
                'key' => 'allow_registration',
                'value' => '1',
                'group' => 'system',
                'type' => 'boolean',
                'label' => 'Izinkan Registrasi Baru',
            ],
            [
                'key' => 'theme_color',
                'value' => '#666cff',
                'group' => 'general',
                'type' => 'color',
                'label' => 'Warna Tema Utama',
            ],

            // Profil Website (halaman landing + footer)
            [
                'key' => 'profile_kecamatan',
                'value' => 'Kecamatan Soreang',
                'group' => 'profil',
                'type' => 'text',
                'label' => 'Nama Kecamatan',
            ],
            [
                'key' => 'profile_kota',
                'value' => 'Kota Parepare',
                'group' => 'profil',
                'type' => 'text',
                'label' => 'Nama Kota/Kabupaten',
            ],
            [
                'key' => 'profile_alamat',
                'value' => 'Jl. Jenderal Sudirman No. 45, Kecamatan Soreang, Kota Parepare, Sulawesi Selatan 91131',
                'group' => 'profil',
                'type' => 'textarea',
                'label' => 'Alamat Kantor',
            ],
            [
                'key' => 'profile_telepon',
                'value' => '(0421) 21055',
                'group' => 'profil',
                'type' => 'text',
                'label' => 'Telepon Kantor',
            ],
            [
                'key' => 'profile_email',
                'value' => 'layanan@soreang.parepare.go.id',
                'group' => 'profil',
                'type' => 'text',
                'label' => 'Email Kontak',
            ],
            [
                'key' => 'profile_map_embed',
                'value' => null,
                'group' => 'profil',
                'type' => 'textarea',
                'label' => 'Embed Map (iframe URL)',
            ],
            [
                'key' => 'social_facebook',
                'value' => null,
                'group' => 'contact',
                'type' => 'text',
                'label' => 'URL Facebook',
            ],
            [
                'key' => 'social_youtube',
                'value' => null,
                'group' => 'contact',
                'type' => 'text',
                'label' => 'URL Youtube',
            ],

            // WhatsApp notifier (adapter pattern — lihat S3_MVP_DESIGN.md §5.4)
            [
                'key' => 'wa_driver',
                'value' => 'log',
                'group' => 'whatsapp',
                'type' => 'text',
                'label' => 'Driver Notifikasi WA (log/gateway)',
            ],
            [
                'key' => 'wa_gateway_url',
                'value' => null,
                'group' => 'whatsapp',
                'type' => 'text',
                'label' => 'URL Gateway WA',
            ],
            [
                'key' => 'wa_gateway_token',
                'value' => null,
                'group' => 'whatsapp',
                'type' => 'text',
                'label' => 'Token Gateway WA',
            ],

            // Penomoran tiket
            [
                'key' => 'tiket_prefix',
                'value' => 'SRG',
                'group' => 'tiket',
                'type' => 'text',
                'label' => 'Prefix Nomor Tiket',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
