<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Seeder;

class JenisSuratSeeder extends Seeder
{
    public function run(): void
    {
        $jenisSurat = [
            [
                'kode' => 'SKD',
                'nama' => 'Surat Keterangan Domisili',
                'deskripsi' => 'Surat keterangan tempat tinggal yang diterbitkan oleh kelurahan.',
                'fields' => [
                    ['name' => 'alamat_domisili', 'type' => 'textarea', 'label' => 'Alamat Domisili', 'required' => true],
                    ['name' => 'sejak_tahun', 'type' => 'number', 'label' => 'Berdomisili Sejak Tahun', 'required' => false],
                    ['name' => 'keperluan_domisili', 'type' => 'text', 'label' => 'Keperluan', 'required' => true],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SKTM',
                'nama' => 'Surat Keterangan Tidak Mampu',
                'deskripsi' => 'Surat keterangan status ekonomi tidak mampu untuk keperluan bantuan/beasiswa.',
                'fields' => [
                    ['name' => 'pekerjaan', 'type' => 'text', 'label' => 'Pekerjaan', 'required' => true],
                    ['name' => 'penghasilan_per_bulan', 'type' => 'number', 'label' => 'Penghasilan per Bulan (Rp)', 'required' => false],
                    ['name' => 'jumlah_tanggungan', 'type' => 'number', 'label' => 'Jumlah Tanggungan', 'required' => false],
                    ['name' => 'keperluan_sktm', 'type' => 'text', 'label' => 'Keperluan', 'required' => true],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SKU',
                'nama' => 'Surat Keterangan Usaha',
                'deskripsi' => 'Surat keterangan kepemilikan usaha untuk keperluan perizinan/perbankan.',
                'fields' => [
                    ['name' => 'nama_usaha', 'type' => 'text', 'label' => 'Nama Usaha', 'required' => true],
                    ['name' => 'jenis_usaha', 'type' => 'text', 'label' => 'Jenis Usaha', 'required' => true],
                    ['name' => 'alamat_usaha', 'type' => 'textarea', 'label' => 'Alamat Usaha', 'required' => true],
                    ['name' => 'tahun_mulai', 'type' => 'number', 'label' => 'Tahun Mulai Usaha', 'required' => false],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SKBM',
                'nama' => 'Surat Keterangan Belum Menikah',
                'deskripsi' => 'Surat keterangan status belum menikah untuk keperluan administrasi.',
                'fields' => [
                    ['name' => 'nama_ayah', 'type' => 'text', 'label' => 'Nama Ayah', 'required' => true],
                    ['name' => 'nama_ibu', 'type' => 'text', 'label' => 'Nama Ibu', 'required' => true],
                    ['name' => 'keperluan', 'type' => 'text', 'label' => 'Keperluan', 'required' => true],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SPKTP',
                'nama' => 'Surat Pengantar KTP',
                'deskripsi' => 'Surat pengantar untuk pembuatan/perpanjangan/penggantian KTP di Disdukcapil.',
                'fields' => [
                    ['name' => 'jenis_permohonan', 'type' => 'select', 'label' => 'Jenis Permohonan', 'options' => ['Baru', 'Perpanjangan', 'Penggantian'], 'required' => true],
                ],
                'wajib_pengantar_rt_rw' => false,
                'is_active' => true,
            ],
            [
                'kode' => 'SPKK',
                'nama' => 'Surat Pengantar Kartu Keluarga',
                'deskripsi' => 'Surat pengantar untuk pembuatan/perubahan Kartu Keluarga di Disdukcapil.',
                'fields' => [
                    ['name' => 'jenis_permohonan', 'type' => 'select', 'label' => 'Jenis Permohonan', 'options' => ['Baru', 'Perubahan Data', 'Penggantian'], 'required' => true],
                    ['name' => 'jumlah_anggota', 'type' => 'number', 'label' => 'Jumlah Anggota Keluarga', 'required' => false],
                ],
                'wajib_pengantar_rt_rw' => false,
                'is_active' => true,
            ],
            [
                'kode' => 'SKKL',
                'nama' => 'Surat Keterangan Kelahiran',
                'deskripsi' => 'Surat keterangan kelahiran untuk dasar penerbitan akta kelahiran.',
                'fields' => [
                    ['name' => 'nama_bayi', 'type' => 'text', 'label' => 'Nama Bayi', 'required' => true],
                    ['name' => 'tanggal_lahir', 'type' => 'date', 'label' => 'Tanggal Lahir', 'required' => true],
                    ['name' => 'tempat_lahir', 'type' => 'text', 'label' => 'Tempat Lahir', 'required' => true],
                    ['name' => 'nama_ayah', 'type' => 'text', 'label' => 'Nama Ayah', 'required' => true],
                    ['name' => 'nama_ibu', 'type' => 'text', 'label' => 'Nama Ibu', 'required' => true],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SKKM',
                'nama' => 'Surat Keterangan Kematian',
                'deskripsi' => 'Surat keterangan kematian untuk dasar penerbitan akta kematian.',
                'fields' => [
                    ['name' => 'nama_almarhum', 'type' => 'text', 'label' => 'Nama Almarhum/Almarhumah', 'required' => true],
                    ['name' => 'nik_almarhum', 'type' => 'text', 'label' => 'NIK Almarhum/Almarhumah', 'required' => true],
                    ['name' => 'tanggal_meninggal', 'type' => 'date', 'label' => 'Tanggal Meninggal', 'required' => true],
                    ['name' => 'tempat_meninggal', 'type' => 'text', 'label' => 'Tempat Meninggal', 'required' => false],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SPSKCK',
                'nama' => 'Surat Pengantar SKCK',
                'deskripsi' => 'Surat pengantar untuk permohonan Surat Keterangan Catatan Kepolisian.',
                'fields' => [
                    ['name' => 'keperluan_skck', 'type' => 'text', 'label' => 'Keperluan', 'required' => true],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SKP',
                'nama' => 'Surat Keterangan Pindah',
                'deskripsi' => 'Surat keterangan pindah domisili untuk proses administrasi kependudukan.',
                'fields' => [
                    ['name' => 'alamat_tujuan', 'type' => 'textarea', 'label' => 'Alamat Tujuan Pindah', 'required' => true],
                    ['name' => 'alasan_pindah', 'type' => 'text', 'label' => 'Alasan Pindah', 'required' => true],
                    ['name' => 'jumlah_pengikut', 'type' => 'number', 'label' => 'Jumlah Anggota Keluarga yang Ikut Pindah', 'required' => false],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
        ];

        foreach ($jenisSurat as $jenis) {
            JenisSurat::updateOrCreate(['kode' => $jenis['kode']], $jenis);
        }
    }
}
