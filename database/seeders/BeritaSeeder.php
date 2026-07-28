<?php

namespace Database\Seeders;

use App\Models\Berita;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $berita = [
            [
                'judul' => 'Peluncuran Resmi Soreang Smart Service (3S) — Layanan Publik Kini Lebih Mudah',
                'slug' => 'peluncuran-soreang-smart-service',
                'kategori' => 'Pengumuman',
                'ringkasan' => 'Kecamatan Soreang resmi meluncurkan platform digital Soreang Smart Service (3S) yang mengintegrasikan seluruh layanan administrasi kelurahan dalam satu genggaman.',
                'isi' => '<p>Kecamatan Soreang dengan bangga mempersembahkan <strong>Soreang Smart Service (3S)</strong>, sebuah terobosan digital dalam pelayanan publik yang bertujuan mempercepat, mempermudah, dan meningkatkan transparansi layanan administrasi bagi seluruh warga.</p><p>Melalui platform ini, warga dapat mengajukan berbagai surat keterangan secara online, memantau status permohonan secara real-time, dan menyampaikan pengaduan tanpa harus datang langsung ke kantor kelurahan.</p><p>"Ini adalah wujud nyata komitmen kami untuk memberikan pelayanan yang cepat, mudah, transparan, dan melayani dengan hati," ujar Camat Parepare dalam sambutan peluncuran.</p>',
                'thumbnail' => null,
                'penulis' => 'Admin Kecamatan Soreang',
                'published_at' => now()->subDays(7),
                'is_published' => true,
            ],
            [
                'judul' => 'Cara Mengajukan Surat Keterangan Online via 3S — Panduan Lengkap untuk Warga',
                'slug' => 'cara-mengajukan-surat-online-3s',
                'kategori' => 'Panduan',
                'ringkasan' => 'Panduan langkah demi langkah cara menggunakan fitur Smart Digital Service untuk mengajukan berbagai surat keterangan secara online tanpa perlu antri.',
                'isi' => '<p>Kini warga Kecamatan Soreang tidak perlu lagi mengantri di kantor kelurahan untuk mengurus surat-menyurat. Melalui <strong>Soreang Smart Service (3S)</strong>, seluruh proses dapat dilakukan secara digital.</p><h3>Langkah-langkah:</h3><ol><li>Kunjungi portal 3S atau scan QR Code di kantor kelurahan</li><li>Daftar atau masuk dengan NIK Anda</li><li>Pilih jenis surat yang dibutuhkan</li><li>Isi formulir online dan upload dokumen pendukung</li><li>Dapatkan nomor tiket untuk tracking status</li><li>Surat siap diambil atau dikirim secara digital</li></ol><p>Untuk informasi lebih lanjut, hubungi petugas kelurahan terdekat.</p>',
                'thumbnail' => null,
                'penulis' => 'Tim Digital Kecamatan',
                'published_at' => now()->subDays(3),
                'is_published' => true,
            ],
            [
                'judul' => 'Program Smart UMKM: Dukungan Digital untuk Pelaku Usaha Kecil Soreang',
                'slug' => 'program-smart-umkm-soreang',
                'kategori' => 'Program',
                'ringkasan' => 'Kecamatan Soreang meluncurkan program Smart UMKM sebagai bagian dari ekosistem 3S untuk membantu pelaku usaha mikro dan kecil mendapatkan akses informasi, perizinan, dan promosi digital.',
                'isi' => '<p>Dalam rangka mendorong pertumbuhan ekonomi lokal, Kecamatan Soreang meluncurkan program <strong>Smart UMKM</strong> sebagai salah satu komponen dari ekosistem Soreang Smart Service (3S).</p><p>Program ini memberikan kemudahan bagi pelaku UMKM dalam:</p><ul><li>Mengurus surat keterangan usaha secara online</li><li>Mendapatkan informasi pelatihan dan pendampingan usaha</li><li>Promosi produk melalui portal digital kecamatan</li><li>Akses ke program bantuan modal UMKM</li></ul><p>Daftarkan usaha Anda melalui menu Smart UMKM di platform 3S hari ini!</p>',
                'thumbnail' => null,
                'penulis' => 'Bagian Perekonomian Kecamatan',
                'published_at' => now()->subDay(),
                'is_published' => true,
            ],
        ];

        foreach ($berita as $item) {
            Berita::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
