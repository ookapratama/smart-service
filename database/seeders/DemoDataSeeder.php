<?php

namespace Database\Seeders;

use App\Enums\TiketChannel;
use App\Enums\TiketStatus;
use App\Models\Agenda;
use App\Models\Galeri;
use App\Models\JenisSurat;
use App\Models\KategoriPengaduan;
use App\Models\Kelurahan;
use App\Models\Pemohon;
use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Models\Role;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seed data contoh (pemohon, tiket, agenda, galeri) untuk demo/testing.
 * Selalu dipanggil DatabaseSeeder — termasuk di production, sampai app ini
 * berhenti jadi tahap demo/staging. Idempotent: aman dijalankan berulang
 * setiap deploy (updateOrCreate by nik/nomor_tiket/judul, tidak menduplikasi).
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $bhr = Kelurahan::where('kode', 'BHR')->first();
        $lks = Kelurahan::where('kode', 'LKS')->first();
        $wsr = Kelurahan::where('kode', 'WSR')->first();

        // 1. Pemohon demo
        $pemohon1 = Pemohon::updateOrCreate(
            ['nik' => '7371012501900001'],
            ['name' => 'Andi Wijaya', 'phone' => '081234500001', 'email' => 'andi.wijaya@example.com', 'alamat' => 'Jl. Melati No. 12', 'kelurahan_id' => $bhr?->id]
        );
        $pemohon2 = Pemohon::updateOrCreate(
            ['nik' => '7371015803850002'],
            ['name' => 'Siti Nurhaliza', 'phone' => '081234500002', 'email' => 'siti.nurhaliza@example.com', 'alamat' => 'Jl. Anggrek No. 7', 'kelurahan_id' => $lks?->id]
        );
        $pemohon3 = Pemohon::updateOrCreate(
            ['nik' => '7371011207920003'],
            ['name' => 'Muhammad Yusuf', 'phone' => '081234500003', 'email' => 'muhammad.yusuf@example.com', 'alamat' => 'Jl. Kenanga No. 3', 'kelurahan_id' => $wsr?->id]
        );

        // 2. Akun warga demo — passwordless (login OTP di fase berikutnya), di-link ke pemohon1
        $wargaRole = Role::where('slug', 'warga')->first();
        if ($wargaRole) {
            $wargaUser = User::updateOrCreate(
                ['email' => 'warga.demo@gmail.com'],
                ['name' => $pemohon1->name, 'password' => null, 'role_id' => $wargaRole->id]
            );
            $pemohon1->user_id = $wargaUser->id;
            $pemohon1->save();
        }

        $kategori = KategoriPengaduan::first();
        $jenisSurat = JenisSurat::orderBy('id')->get();

        // 3. Tiket pengaduan demo — nomor SRG-DEMO-0000N, tidak menyentuh tiket_counters
        $pengaduanSeed = [
            [
                'nomor' => 'SRG-DEMO-00001',
                'pemohon' => $pemohon1,
                'status' => TiketStatus::Baru,
                'is_anonim' => false,
                'judul' => 'Lampu Jalan Mati di Jl. Melati',
                'jenis_laporan' => 'Pengaduan / Keluhan',
                'logs' => [
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Baru, 'catatan' => 'Laporan diterima via portal 3S.'],
                ],
            ],
            [
                'nomor' => 'SRG-DEMO-00002',
                'pemohon' => $pemohon2,
                'status' => TiketStatus::Diproses,
                'is_anonim' => true,
                'judul' => 'Sampah Menumpuk di TPS Sementara',
                'jenis_laporan' => 'Pengaduan / Keluhan',
                'logs' => [
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Baru, 'catatan' => 'Laporan diterima via portal 3S.'],
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Diproses, 'catatan' => 'Ditugaskan ke petugas kebersihan.'],
                ],
            ],
            [
                'nomor' => 'SRG-DEMO-00003',
                'pemohon' => $pemohon3,
                'status' => TiketStatus::Selesai,
                'is_anonim' => false,
                'judul' => 'Usulan Perbaikan Trotoar',
                'jenis_laporan' => 'Aspirasi / Usulan',
                'logs' => [
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Baru, 'catatan' => 'Laporan diterima via portal 3S.'],
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Diproses, 'catatan' => 'Survei lokasi oleh petugas.'],
                    ['from' => TiketStatus::Diproses, 'to' => TiketStatus::Selesai, 'catatan' => 'Perbaikan trotoar selesai dikerjakan.'],
                ],
            ],
            [
                'nomor' => 'SRG-DEMO-00004',
                'pemohon' => $pemohon1,
                'status' => TiketStatus::Ditolak,
                'is_anonim' => false,
                'judul' => 'Keluhan Duplikat Lampu Jalan',
                'jenis_laporan' => 'Kritik & Saran',
                'logs' => [
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Baru, 'catatan' => 'Laporan diterima via portal 3S.'],
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Ditolak, 'catatan' => 'Duplikat dari tiket SRG-DEMO-00001.'],
                ],
            ],
        ];

        foreach ($pengaduanSeed as $seed) {
            if (Tiket::where('nomor_tiket', $seed['nomor'])->exists()) {
                continue;
            }

            $pengaduan = Pengaduan::create([
                'kategori_pengaduan_id' => $kategori?->id,
                'deskripsi' => $seed['judul'].' — data demo untuk keperluan testing.',
                'lokasi' => 'Kecamatan Soreang',
                'is_anonim' => $seed['is_anonim'],
                'jenis_laporan' => $seed['jenis_laporan'],
                'tanggal_kejadian' => now()->subDays(random_int(1, 10))->toDateString(),
            ]);

            $tiket = $pengaduan->tiket()->create([
                'nomor_tiket' => $seed['nomor'],
                'pemohon_id' => $seed['pemohon']->id,
                'status' => $seed['status'],
                'channel' => TiketChannel::Web,
                'judul' => $seed['judul'],
                'keterangan' => $pengaduan->deskripsi,
                'selesai_at' => $seed['status'] === TiketStatus::Selesai ? now() : null,
            ]);

            foreach ($seed['logs'] as $log) {
                $tiket->statusLogs()->create([
                    'status_from' => $log['from'],
                    'status_to' => $log['to'],
                    'user_id' => null,
                    'catatan' => $log['catatan'],
                ]);
            }
        }

        // 4. Tiket pengajuan surat demo
        $suratSeed = [
            [
                'nomor' => 'SRG-DEMO-10001',
                'pemohon' => $pemohon2,
                'jenis' => $jenisSurat->firstWhere('kode', 'SKD'),
                'status' => TiketStatus::Baru,
                'nomor_surat' => null,
                'logs' => [
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Baru, 'catatan' => 'Pengajuan diterima, menunggu verifikasi berkas.'],
                ],
            ],
            [
                'nomor' => 'SRG-DEMO-10002',
                'pemohon' => $pemohon3,
                'jenis' => $jenisSurat->firstWhere('kode', 'SKU'),
                'status' => TiketStatus::Diproses,
                'nomor_surat' => null,
                'logs' => [
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Baru, 'catatan' => 'Pengajuan diterima, menunggu verifikasi berkas.'],
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Diproses, 'catatan' => 'Berkas lengkap, surat sedang disiapkan.'],
                ],
            ],
            [
                'nomor' => 'SRG-DEMO-10003',
                'pemohon' => $pemohon1,
                'jenis' => $jenisSurat->firstWhere('kode', 'SKTM'),
                'status' => TiketStatus::Selesai,
                'nomor_surat' => '474/DEMO-001/KEC-SRG/'.now()->format('Y'),
                'logs' => [
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Baru, 'catatan' => 'Pengajuan diterima, menunggu verifikasi berkas.'],
                    ['from' => TiketStatus::Baru, 'to' => TiketStatus::Diproses, 'catatan' => 'Berkas lengkap, surat sedang disiapkan.'],
                    ['from' => TiketStatus::Diproses, 'to' => TiketStatus::Selesai, 'catatan' => 'Surat terbit dan siap diambil.'],
                ],
            ],
        ];

        foreach ($suratSeed as $seed) {
            if (! $seed['jenis'] || Tiket::where('nomor_tiket', $seed['nomor'])->exists()) {
                continue;
            }

            $pengajuan = PengajuanSurat::create([
                'jenis_surat_id' => $seed['jenis']->id,
                'keperluan' => 'Keperluan administrasi — data demo untuk keperluan testing.',
                'data' => ['catatan_demo' => true],
                'nomor_surat' => $seed['nomor_surat'],
            ]);

            $tiket = $pengajuan->tiket()->create([
                'nomor_tiket' => $seed['nomor'],
                'pemohon_id' => $seed['pemohon']->id,
                'status' => $seed['status'],
                'channel' => TiketChannel::Web,
                'judul' => 'Pengajuan '.$seed['jenis']->nama,
                'keterangan' => $pengajuan->keperluan,
                'selesai_at' => $seed['status'] === TiketStatus::Selesai ? now() : null,
            ]);

            foreach ($seed['logs'] as $log) {
                $tiket->statusLogs()->create([
                    'status_from' => $log['from'],
                    'status_to' => $log['to'],
                    'user_id' => null,
                    'catatan' => $log['catatan'],
                ]);
            }
        }

        // 5. Agenda & Galeri demo (untuk konten landing fase berikutnya)
        Agenda::updateOrCreate(
            ['judul' => 'Musyawarah Rencana Pembangunan Kecamatan'],
            [
                'deskripsi' => 'Musrenbang tahunan tingkat kecamatan untuk menyusun prioritas pembangunan.',
                'lokasi' => 'Aula Kantor Kecamatan Soreang',
                'mulai_at' => now()->addDays(14)->setTime(9, 0),
                'selesai_at' => now()->addDays(14)->setTime(12, 0),
                'is_published' => true,
            ]
        );
        Agenda::updateOrCreate(
            ['judul' => 'Pelayanan Keliling Administrasi Kependudukan'],
            [
                'deskripsi' => 'Layanan jemput bola pembuatan KTP dan KK di kelurahan.',
                'lokasi' => 'Lapangan Kelurahan Lakessi',
                'mulai_at' => now()->addDays(7)->setTime(8, 0),
                'selesai_at' => now()->addDays(7)->setTime(14, 0),
                'is_published' => true,
            ]
        );

        Galeri::updateOrCreate(
            ['judul' => 'Peluncuran Soreang Smart Service'],
            ['keterangan' => 'Dokumentasi acara peluncuran platform 3S di Kantor Kecamatan Soreang.', 'is_published' => true]
        );
        Galeri::updateOrCreate(
            ['judul' => 'Kegiatan Gotong Royong Kelurahan'],
            ['keterangan' => 'Kegiatan gotong royong kebersihan lingkungan bersama warga.', 'is_published' => true]
        );
    }
}
