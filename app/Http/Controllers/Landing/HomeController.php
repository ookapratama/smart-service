<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\JadwalPelayanan;
use App\Models\JenisSurat;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Halaman panduan penggunaan layanan 3S (bahasa awam untuk warga).
     */
    public function panduan(): View
    {
        return view('home.panduan');
    }

    /**
     * Display the 3S (Soreang Smart Service) landing home page.
     */
    public function index(): View
    {
        // 1. Informasi Produk
        $siteInfo = [
            'name' => 'SOREANG SMART SERVICE',
            'code' => '3S',
            'tagline' => 'Cepat, Mudah, Transparan, dan Melayani dengan Hati.',
            'description' => 'Sistem Pelayanan Publik Terintegrasi Berbasis Digital dan Kolaboratif Kecamatan Soreang',
            'email' => 'layanan@soreang.parepare.go.id',
            'phone' => '(0421) 21055',
            'whatsapp' => '+6281234567890',
            'address_line1' => 'Jl. Laupe Kota Parepare, Sulawesi Selatan 91131',
            'address_line2' => '',
            'about_short' => 'Platform digital terpadu untuk pengurusan surat online, pengaduan masyarakat, monitoring real-time, dan kolaborasi antarkelurahan.',
            'video_url' => 'https://www.youtube.com/watch?v=Y7f98aduVJ8',
            'social_links' => [
                'twitter' => 'https://twitter.com/soreang_smart',
                'facebook' => 'https://facebook.com/soreangsmartservice',
                'instagram' => 'https://instagram.com/soreang.smartservice',
                'youtube' => 'https://youtube.com/c/SoreangSmartService',
            ],
        ];

        // 2. Section Masalah & Solusi (Why 3S Needed?)
        $challenges = [
            [
                'icon' => 'bi-file-earmark-x',
                'title' => 'Pelayanan Administrasi Belum Terintegrasi',
                'description' => 'Proses manual di masing-masing kelurahan menyebabkan inefisiensi data dan duplikasi berkas.',
            ],
            [
                'icon' => 'bi-person-walking',
                'title' => 'Masyarakat Harus Datang Langsung',
                'description' => 'Membuang waktu dan biaya perjalanan hanya untuk mengajukan atau mengecek status surat.',
            ],
            [
                'icon' => 'bi-chat-left-dots',
                'title' => 'Pengaduan Tidak Terdokumentasi',
                'description' => 'Aspirasi dan keluhan warga sering tercecer tanpa penanganan dan kepastian tindak lanjut.',
            ],
            [
                'icon' => 'bi-graph-down',
                'title' => 'Monitoring Belum Berbasis Data',
                'description' => 'Evaluasi kinerja kelurahan masih mengandalkan rekap manual yang lambat dan rentan bias.',
            ],
            [
                'icon' => 'bi-node-minus',
                'title' => 'Koordinasi Antar Kelurahan Suboptimal',
                'description' => 'Silo antar-wilayah menghambat pertukaran informasi dan pelayanan lintas wilayah.',
            ],
        ];

        // 3. Section Fitur Utama (8 Smart Components)
        $smartComponents = [
            [
                'id' => 1,
                'title' => 'Smart Digital Service',
                'icon' => '📋',
                'bs_icon' => 'bi-file-earmark-text',
                'description' => 'Pengurusan berbagai jenis surat keterangan online dilengkapi nomor tiket tracking otomatis.',
                'link' => '#jenis-surat-modal',
                'link_text' => 'Lihat Jenis Surat',
                'badge' => 'Terpopuler',
            ],
            [
                'id' => 2,
                'title' => 'Smart Complaint',
                'icon' => '💬',
                'bs_icon' => 'bi-whatsapp',
                'description' => 'Pengaduan publik multi-channel terpadu via WhatsApp, Portal Web, dan QR Code terintegrasi.',
                'link' => route('pengaduan.index'),
                'link_text' => 'Buat Pengaduan',
                'badge' => 'Responsif',
            ],
            [
                'id' => 3,
                'title' => 'Smart Monitoring Dashboard',
                'icon' => '📊',
                'bs_icon' => 'bi-speedometer2',
                'description' => 'Monitoring KPI pelayanan publik, durasi penyelesaian, dan volume permohonan secara real-time.',
                'link' => route('login'),
                'link_text' => 'Lihat Dashboard',
                'badge' => 'Khusus Admin',
            ],
            [
                'id' => 4,
                'title' => 'Smart Village Collaboration',
                'icon' => '🔗',
                'bs_icon' => 'bi-diagram-3',
                'description' => 'Sistem integrasi data dan koordinasi terpadu seluruh kelurahan se-Kecamatan Soreang.',
                'link' => '#keunggulan',
                'link_text' => 'Pelajari Integrasi',
                'badge' => 'Satu Data',
            ],
            [
                'id' => 5,
                'title' => 'Smart Citizen',
                'icon' => '👤',
                'bs_icon' => 'bi-person-badge',
                'description' => 'Portal khusus warga untuk memantau histori permohonan, profil, dan QR Code identitas permohonan.',
                'link' => route('login'),
                'link_text' => 'Portal Warga',
                'badge' => 'Akses Warga',
            ],
            [
                'id' => 6,
                'title' => 'Smart Emergency',
                'icon' => '🚨',
                'bs_icon' => 'bi-shield-exclamation',
                'description' => 'Layanan tanggap darurat cepat 24/7 terkoneksi ke satpol PP, pemadam, dan ambulans kelurahan.',
                'link' => 'tel:112',
                'link_text' => 'Kontak Darurat 24/7',
                'badge' => 'Siaga 24/7',
            ],
            [
                'id' => 7,
                'title' => 'Smart UMKM',
                'icon' => '🏪',
                'bs_icon' => 'bi-shop',
                'description' => 'Dukungan pengurusan izin usaha mikro, promosi produk digital, dan pemberdayaan pelaku ekonomi lokal.',
                'link' => route('berita.public.index'),
                'link_text' => 'Info Program UMKM',
                'badge' => 'Ekonomi',
            ],
            [
                'id' => 8,
                'title' => 'Smart Data Center',
                'icon' => '💾',
                'bs_icon' => 'bi-database-check',
                'description' => 'Pusat repositori data kependudukan dan statistik wilayah terintegrasi dan aman.',
                'link' => '#keunggulan',
                'link_text' => 'Keamanan Data',
                'badge' => 'Secure cloud',
            ],
        ];

        // 4. Section Keunggulan (Advantages - 3 kolom layout)
        $advantages = [
            [
                'icon' => 'bi-layers-half',
                'title' => 'Terintegrasi Lintas Kelurahan',
                'description' => 'Menghubungkan kantor kecamatan dan 7 kelurahan secara seamless dalam satu pangkalan data.',
            ],
            [
                'icon' => 'bi-bar-chart-line-fill',
                'title' => 'Berbasis Data & Dashboard Real-Time',
                'description' => 'Setiap pimpinan dan petugas dapat memantau progres permohonan dan bottleneck secara langsung.',
            ],
            [
                'icon' => 'bi-phone-vibrate',
                'title' => 'Mudah Diakses via Smartphone',
                'description' => 'Desain ultra-responsif yang nyaman digunakan dari HP, tablet, maupun komputer desktop.',
            ],
            [
                'icon' => 'bi-eye-fill',
                'title' => 'Transparansi & Monitoring Publik',
                'description' => 'Warga dapat mengecek status permohonan kapan saja menggunakan NIK atau Nomor Tiket.',
            ],
            [
                'icon' => 'bi-file-earmark-check-fill',
                'title' => 'Paperless & Ramah Lingkungan',
                'description' => 'Mengurangi penggunaan dokumen fisik dan biaya cetak hingga 80% melalui validasi QR Code.',
            ],
            [
                'icon' => 'bi-trophy-fill',
                'title' => 'KPI Terukur & Terdata',
                'description' => 'Kecepatan dan kualitas pelayanan dipantau berdasarkan standar SLA yang akuntabel.',
            ],
        ];

        // 5. Section Indikator Keberhasilan (Stats/Metrics)
        $metrics = [
            [
                'count' => '75',
                'suffix' => '%',
                'label' => 'Pengurangan Waktu Layanan',
                'description' => 'Dari rata-rata 3 hari menjadi hitungan jam',
                'icon' => 'bi-clock-history',
            ],
            [
                'count' => '94.8',
                'suffix' => '%',
                'label' => 'Indeks Kepuasan Masyarakat',
                'description' => 'Kategori Sangat Baik (Survei 2026)',
                'icon' => 'bi-emoji-smile-fill',
            ],
            [
                'count' => '24',
                'suffix' => ' Jam',
                'label' => 'Target Maksimal Pengaduan',
                'description' => 'Respon dan tindak lanjut laporan warga',
                'icon' => 'bi-check2-circle',
            ],
            [
                'count' => '100',
                'suffix' => '%',
                'label' => 'Coverage Kelurahan',
                'description' => 'Terhubung di 7 Kelurahan Kecamatan Soreang',
                'icon' => 'bi-geo-alt-fill',
            ],
            [
                'count' => '150',
                'suffix' => '%+',
                'label' => 'Pertumbuhan Layanan Digital',
                'description' => 'Peningkatan pengajuan online per tahun',
                'icon' => 'bi-graph-up-arrow',
            ],
        ];

        // 6. Section Galeri / Screenshot Sistem
        $screenshots = [
            [
                'title' => 'Dashboard Monitoring Utama Admin',
                'description' => 'Visualisasi data real-time statistik surat, pengaduan, dan kinerja petugas per kelurahan.',
                'image' => asset('assets/home/img/clients/client-1.png'),
                'tag' => 'Dashboard',
            ],
            [
                'title' => 'Form Pengajuan Surat & Tiket Digital',
                'description' => 'Pengisian data pemohon ringkas dengan unggah dokumen syarat dan generasi nomor tiket instan.',
                'image' => asset('assets/home/img/clients/client-2.png'),
                'tag' => 'Pelayanan',
            ],
            [
                'title' => 'Portal Tracking Status Permohonan',
                'description' => 'Pencarian status real-time berbasis NIK atau Nomor Tiket tanpa wajib login.',
                'image' => asset('assets/home/img/clients/client-3.png'),
                'tag' => 'Tracking',
            ],
            [
                'title' => 'Verifikasi Otentikasi QR Code',
                'description' => 'Setiap tiket & dokumen dilengkapi QR code enkripsi untuk verifikasi keaslian di lapangan.',
                'image' => asset('assets/home/img/clients/client-4.png'),
                'tag' => 'QR Code',
            ],
        ];

        // 7. Berita & Kegiatan Terbaru
        $beritaList = Berita::published()->latest('published_at')->take(3)->get();
        if ($beritaList->isEmpty()) {
            $beritaList = collect([
                (object) [
                    'judul' => 'Peluncuran Resmi Soreang Smart Service (3S)',
                    'slug' => 'peluncuran-resmi-3s',
                    'kategori' => 'Pengumuman',
                    'ringkasan' => 'Kecamatan Soreang resmi meluncurkan platform digital 3S untuk seluruh pelayanan kelurahan.',
                    'published_at' => now()->subDays(5),
                    'penulis' => 'Admin Kecamatan',
                ],
                (object) [
                    'judul' => 'Panduan Pengajuan Surat Online via 3S',
                    'slug' => 'panduan-pengajuan-surat-online',
                    'kategori' => 'Panduan',
                    'ringkasan' => 'Dapatkan surat keterangan tanpa perlu antri di kantor kelurahan hanya dalam 3 langkah.',
                    'published_at' => now()->subDays(2),
                    'penulis' => 'Tim Digital 3S',
                ],
                (object) [
                    'judul' => 'Program Smart UMKM Soreang Buka Pendaftaran',
                    'slug' => 'program-smart-umkm-soreang',
                    'kategori' => 'Program',
                    'ringkasan' => 'Bantuan perizinan gratis dan promosi digital untuk pelaku UMKM lokal.',
                    'published_at' => now()->subDay(),
                    'penulis' => 'Bidang Ekonomi',
                ],
            ]);
        }

        // 8. Jadwal Pelayanan per Kelurahan
        $jadwalList = JadwalPelayanan::active()->with('kelurahan')->get();

        // 9. Daftar Jenis Surat untuk Modal Quick View
        $jenisSuratList = JenisSurat::where('is_active', true)->get();

        // 10. FAQ (Pertanyaan Umum)
        $faqs = [
            [
                'question' => 'Bagaimana cara mengajukan surat keterangan secara online di 3S?',
                'answer' => 'Warga dapat mengeklik tombol "Mulai Sekarang" atau langsung memilih menu Smart Digital Service. Masukkan NIK Anda, pilih jenis surat yang dibutuhkan, lengkapi berkas persyaratan, lalu submit. Anda akan mendapatkan Nomor Tiket untuk melacak prosesnya.',
            ],
            [
                'question' => 'Berapa lama proses penyelesaian permohonan surat?',
                'answer' => 'Rata-rata waktu penyelesaian surat keterangan adalah 1 hingga 24 jam kerja tergantung kelengkapan berkas dan pengantar RT/RW. Anda dapat memantau status secara live melalui fitur Cek Status.',
            ],
            [
                'question' => 'Bagaimana cara mengecek status permohonan surat saya?',
                'answer' => 'Masuk ke halaman "Cek Status" di menu navigasi, masukkan Nomor Tiket (contoh: SRG-2607-00123) atau NIK Anda, lalu klik "Cari Tiket". Hasil progres akan ditampilkan seketika.',
            ],
            [
                'question' => 'Apakah pengaduan lewat WhatsApp dan Website ditindaklanjuti?',
                'answer' => 'Ya, setiap pengaduan yang masuk via WhatsApp resmi 3S maupun Web akan diteruskan langsung ke sistem kelurahan/kecamatan terkait dengan batas respon maksimal 24 jam kerja.',
            ],
            [
                'question' => 'Apakah pengajuan surat online dipungut biaya?',
                'answer' => 'Tidak. Seluruh pelayanan publik di Kecamatan Soreang melalui platform Soreang Smart Service (3S) adalah GRATIS (Rp 0).',
            ],
            [
                'question' => 'Bagaimana jika surat membutuhkan tanda tangan dan pengantar RT/RW?',
                'answer' => 'Beberapa jenis surat mensyaratkan foto/scan surat pengantar RT/RW. Anda tinggal mengunggah foto surat pengantar tersebut saat mengisi formulir online di sistem.',
            ],
            [
                'question' => 'Bagaimana cara kerja validasi QR Code pada tiket 3S?',
                'answer' => 'Setiap permohonan menghasilkan QR Code unik. Saat QR Code di-scan menggunakan kamera smartphone, sistem akan langsung membuka halaman verifikasi resmi untuk membuktikan keaslian dokumen.',
            ],
        ];

        return view('home.index', compact(
            'siteInfo',
            'challenges',
            'smartComponents',
            'advantages',
            'metrics',
            'screenshots',
            'beritaList',
            'jadwalList',
            'jenisSuratList',
            'faqs'
        ));
    }
}
