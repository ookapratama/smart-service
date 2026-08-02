<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'judul' => 'Rapat Musrenbang Pembangunan Kecamatan Soreang Tahun 2026',
                'slug' => 'rapat-musrenbang-pembangunan-kecamatan-soreang-2026',
                'kategori' => 'Rapat & Musyawarah',
                'penyelenggara' => 'Pemerintah Kecamatan Soreang',
                'lokasi' => 'Aula Utama Kantor Kecamatan Soreang',
                'mulai_at' => now()->addDays(3)->setHour(9)->setMinute(0),
                'selesai_at' => now()->addDays(3)->setHour(13)->setMinute(0),
                'waktu_keterangan' => '09:00 - 13:00 WITA',
                'ringkasan' => 'Musyawarah Perencanaan Pembangunan tingkat kecamatan untuk pembahasan usulan prioritas kelurahan.',
                'deskripsi' => 'Rapat Musrenbang Kecamatan Soreang akan dihadiri oleh Camat, para Lurah se-Kecamatan Soreang, perwakilan Bappeda Kota Parepare, tokoh masyarakat, serta delegasi RT/RW.',
                'status_agenda' => 'mendatang',
                'is_published' => true,
            ],
            [
                'judul' => 'Pelayanan Administrasi Kependudukan Keliling di Kelurahan Lakessi',
                'slug' => 'pelayanan-administrasi-kependudukan-keliling-lakessi',
                'kategori' => 'Pelayanan Keliling',
                'penyelenggara' => 'Tim 3S Kecamatan Soreang',
                'lokasi' => 'Office Park Kelurahan Lakessi',
                'mulai_at' => now()->addDays(7)->setHour(8)->setMinute(30),
                'selesai_at' => now()->addDays(7)->setHour(15)->setMinute(0),
                'waktu_keterangan' => '08:30 - 15:00 WITA',
                'ringkasan' => 'Layanan jemput bola pembuatan surat keterangan, perekaman e-KTP, dan sosialisasi aktivasi IKD.',
                'deskripsi' => 'Tim pelayanan mobile Kecamatan Soreang membuka loket terbuka untuk kemudahan warga Kelurahan Lakessi mengurus permohonan surat secara langsung di lokasi.',
                'status_agenda' => 'mendatang',
                'is_published' => true,
            ],
            [
                'judul' => 'Sosialisasi Penanganan Kebersihan & Bank Sampah Digital',
                'slug' => 'sosialisasi-penanganan-kebersihan-bank-sampah-digital',
                'kategori' => 'Sosialisasi & Edukasi',
                'penyelenggara' => 'Seksi Pembangunan & Lingkungan Hidup',
                'lokasi' => 'Baruga Kelurahan Ujung Lare',
                'mulai_at' => now()->subDays(5)->setHour(10)->setMinute(0),
                'selesai_at' => now()->subDays(5)->setHour(12)->setMinute(0),
                'waktu_keterangan' => '10:00 - 12:00 WITA',
                'ringkasan' => 'Edukasi pengolahan sampah rumah tangga dan pengenalan aplikasi Bank Sampah.',
                'deskripsi' => 'Kegiatan pengarahan pengelolaan lingkungan bersih berbasis pemberdayaan komunitas warga.',
                'status_agenda' => 'selesai',
                'is_published' => true,
            ],
        ];

        foreach ($items as $item) {
            Agenda::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
