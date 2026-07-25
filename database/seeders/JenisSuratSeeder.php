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
                'kode' => 'SKET',
                'nama' => 'Surat Keterangan',
                'deskripsi' => 'Surat keterangan umum yang diterbitkan oleh kelurahan.',
                'fields' => [
                    ['name' => 'keterangan_untuk', 'type' => 'text', 'label' => 'Keterangan Untuk', 'required' => true],
                ],
                'wajib_pengantar_rt_rw' => true,
                'is_active' => true,
            ],
            [
                'kode' => 'SPGT',
                'nama' => 'Surat Pengantar',
                'deskripsi' => 'Surat pengantar untuk keperluan administrasi lanjutan.',
                'fields' => [
                    ['name' => 'tujuan_instansi', 'type' => 'text', 'label' => 'Tujuan Instansi', 'required' => true],
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
