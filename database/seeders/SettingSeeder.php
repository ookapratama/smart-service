<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
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

            // Banner & Hero Landing Page
            [
                'key' => 'hero_bg',
                'value' => null,
                'group' => 'banner',
                'type' => 'image',
                'label' => 'Gambar Background Banner Hero',
            ],
            [
                'key' => 'hero_image',
                'value' => null,
                'group' => 'banner',
                'type' => 'image',
                'label' => 'Gambar Banner Samping Hero',
            ],
            [
                'key' => 'hero_badge',
                'value' => 'Portal Resmi Kecamatan Soreang • Kota Parepare',
                'group' => 'banner',
                'type' => 'text',
                'label' => 'Teks Badge Hero Banner',
            ],
            [
                'key' => 'hero_title',
                'value' => 'Soreang Smart Service (3S)',
                'group' => 'banner',
                'type' => 'text',
                'label' => 'Judul Utama Hero Banner',
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Pelayanan kependudukan digital terpadu, pengaduan publik, dan portal informasi resmi Kecamatan Soreang Kota Parepare secara cepat, mudah, dan transparan.',
                'group' => 'banner',
                'type' => 'textarea',
                'label' => 'Subjudul / Keterangan Hero Banner',
            ],
            [
                'key' => 'hero_btn1_text',
                'value' => 'Pengajuan Surat Online',
                'group' => 'banner',
                'type' => 'text',
                'label' => 'Teks Tombol Utama 1',
            ],
            [
                'key' => 'hero_btn2_text',
                'value' => 'Profil Kecamatan',
                'group' => 'banner',
                'type' => 'text',
                'label' => 'Teks Tombol Utama 2',
            ],
            [
                'key' => 'service_image',
                'value' => null,
                'group' => 'banner',
                'type' => 'image',
                'label' => 'Gambar Banner Pelayanan Terpadu (Solusi 3S)',
            ],
            [
                'key' => 'service_badge',
                'value' => 'Solusi Terintegrasi',
                'group' => 'banner',
                'type' => 'text',
                'label' => 'Teks Badge Pelayanan Terpadu',
            ],
            [
                'key' => 'service_title',
                'value' => 'Pelayanan Cepat, Transparan, & Tanpa Antri',
                'group' => 'banner',
                'type' => 'text',
                'label' => 'Judul Banner Pelayanan Terpadu',
            ],
            [
                'key' => 'service_subtitle',
                'value' => 'Warga Kecamatan Soreang kini dapat mengajukan surat keterangan online, melacak tiket status permohonan secara real-time, dan menyampaikan aspirasi tanpa harus datang mengantri di kantor kelurahan.',
                'group' => 'banner',
                'type' => 'textarea',
                'label' => 'Keterangan Banner Pelayanan Terpadu',
            ],

            // Profil Website (halaman landing + footer + kop surat PDF)
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
                'key' => 'profile_visi',
                'value' => '"Parepare Terkemuka & Soreang Smart Sejahtera"',
                'group' => 'profil',
                'type' => 'textarea',
                'label' => 'Visi Utama Kecamatan',
            ],
            [
                'key' => 'profile_deskripsi_lengkap',
                'value' => 'Kecamatan Soreang merupakan salah satu kawasan pusat pemerintahan dan aktivitas ekonomi masyarakat di Kota Parepare. Terdiri dari 7 Kelurahan, Kecamatan Soreang mengusung inovasi pelayanan digital Soreang Smart Service (3S) untuk memudahkan pengurusan surat, pengaduan publik, serta transparansi data kependudukan.',
                'group' => 'profil',
                'type' => 'textarea',
                'label' => 'Deskripsi Ringkas Profil Wilayah',
            ],
            [
                'key' => 'contact_phone',
                'value' => '08123456789',
                'group' => 'profil',
                'type' => 'text',
                'label' => 'Nomor WhatsApp',
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/ookapratama',
                'group' => 'profil',
                'type' => 'text',
                'label' => 'URL Instagram',
            ],
            [
                'key' => 'social_facebook',
                'value' => null,
                'group' => 'profil',
                'type' => 'text',
                'label' => 'URL Facebook',
            ],
            [
                'key' => 'social_youtube',
                'value' => null,
                'group' => 'profil',
                'type' => 'text',
                'label' => 'URL Youtube',
            ],

            // WhatsApp notifier (adapter pattern — lihat S3_MVP_DESIGN.md §5.4)
            [
                'key' => 'wa_driver',
                'value' => 'log',
                'group' => 'whatsapp',
                'type' => 'select',
                'label' => 'Driver Notifikasi WA',
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

            // Penandatangan surat resmi (blok ttd PDF — surat/templates/partials/ttd)
            [
                'key' => 'ttd_jabatan',
                'value' => 'Camat Soreang',
                'group' => 'penandatangan',
                'type' => 'text',
                'label' => 'Jabatan Penandatangan',
            ],
            [
                'key' => 'ttd_nama',
                'value' => null,
                'group' => 'penandatangan',
                'type' => 'text',
                'label' => 'Nama Lengkap Penandatangan',
            ],
            [
                'key' => 'ttd_nip',
                'value' => null,
                'group' => 'penandatangan',
                'type' => 'text',
                'label' => 'NIP Penandatangan',
            ],
        ];

        // db:seed berjalan pada SETIAP deploy (webhook) — baris yang sudah ada hanya
        // boleh diperbarui metadata-nya (group/type/label); `value` adalah milik admin
        // dan tidak boleh direset ke default seeder.
        foreach ($settings as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();

            if ($exists) {
                DB::table('settings')->where('key', $setting['key'])->update(
                    Arr::only($setting, ['group', 'type', 'label']) + ['updated_at' => now()]
                );

                continue;
            }

            DB::table('settings')->insert(
                $setting + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
