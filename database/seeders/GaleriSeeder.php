<?php

namespace Database\Seeders;

use App\Models\Galeri;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'judul' => 'Dokumentasi Pelayanan Kependudukan Keliling Kecamatan Soreang',
                'slug' => 'dokumentasi-pelayanan-kependudukan-keliling',
                'kategori' => 'Pelayanan Publik',
                'keterangan' => 'Kegiatan jemput bola pelayanan administrasi kependudukan dan konsultasi warga di Kelurahan Watang Soreang.',
                'gambar' => null,
                'tipe' => 'foto',
                'video_url' => null,
                'tgl_kegiatan' => '2026-07-20',
                'is_published' => true,
            ],
            [
                'judul' => 'Sosialisasi Penerapan Inovasi Soreang Smart Service (3S)',
                'slug' => 'sosialisasi-penerapan-inovasi-soreang-smart-service-3s',
                'kategori' => 'Sosialisasi & Edukasi',
                'keterangan' => 'Pengenalan portal 3S dan kemudahan pembuatan surat keterangan online kepada ketua RT/RW se-Kecamatan Soreang.',
                'gambar' => null,
                'tipe' => 'foto',
                'video_url' => null,
                'tgl_kegiatan' => '2026-07-22',
                'is_published' => true,
            ],
            [
                'judul' => 'Video Profil Layanan Terpadu Kecamatan Soreang Kota Parepare',
                'slug' => 'video-profil-layanan-terpadu-kecamatan-soreang',
                'kategori' => 'Kegiatan Kecamatan',
                'keterangan' => 'Video perkenalan fasilitas pelayanan publik, alur pengaduan, dan digitalisasi administrasi di Kecamatan Soreang.',
                'gambar' => null,
                'tipe' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=Y7f98aduVJ8',
                'tgl_kegiatan' => '2026-07-25',
                'is_published' => true,
            ],
            [
                'judul' => 'Bazar UMKM Kreatif dan Pemberdayaan Ekonomi Masyarakat Soreang',
                'slug' => 'bazar-umkm-kreatif-pemberdayaan-ekonomi',
                'kategori' => 'UMKM & Ekonomi',
                'keterangan' => 'Pameran produk unggulan pelaku usaha mikro kecil dari 7 kelurahan se-Kecamatan Soreang Kota Parepare.',
                'gambar' => null,
                'tipe' => 'foto',
                'video_url' => null,
                'tgl_kegiatan' => '2026-07-28',
                'is_published' => true,
            ],
        ];

        foreach ($items as $item) {
            Galeri::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
