<?php

namespace Database\Seeders;

use App\Models\JadwalPelayanan;
use Illuminate\Database\Seeder;

class JadwalPelayananSeeder extends Seeder
{
    public function run(): void
    {
        $jadwal = [
            [
                'kelurahan'  => 'Kelurahan Sorean',
                'kecamatan'  => 'Sorean',
                'hari'       => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka'   => '08:00',
                'jam_tutup'  => '15:00',
                'istirahat'  => '12:00 - 13:00',
                'petugas'    => 'Bpk. Ahmad Ridwan',
                'telepon'    => '(0251) 555-0101',
                'keterangan' => 'Pelayanan administrasi umum dan surat-menyurat.',
                'is_active'  => true,
            ],
            [
                'kelurahan'  => 'Kelurahan Mekarjaya',
                'kecamatan'  => 'Sorean',
                'hari'       => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka'   => '08:00',
                'jam_tutup'  => '14:30',
                'istirahat'  => '12:00 - 13:00',
                'petugas'    => 'Ibu Sari Dewi',
                'telepon'    => '(0251) 555-0102',
                'keterangan' => 'Pelayanan kependudukan dan administrasi warga.',
                'is_active'  => true,
            ],
            [
                'kelurahan'  => 'Kelurahan Sukamaju',
                'kecamatan'  => 'Sorean',
                'hari'       => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka'   => '07:30',
                'jam_tutup'  => '15:00',
                'istirahat'  => '12:00 - 13:00',
                'petugas'    => 'Bpk. Doni Firmansyah',
                'telepon'    => '(0251) 555-0103',
                'keterangan' => 'Melayani surat pengantar, keterangan domisili, dan layanan UMKM.',
                'is_active'  => true,
            ],
            [
                'kelurahan'  => 'Kelurahan Cibunar',
                'kecamatan'  => 'Sorean',
                'hari'       => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka'   => '08:00',
                'jam_tutup'  => '15:00',
                'istirahat'  => '11:30 - 12:30',
                'petugas'    => 'Ibu Rina Hastuti',
                'telepon'    => '(0251) 555-0104',
                'keterangan' => 'Pelayanan surat kematian, kelahiran, dan pengantar nikah.',
                'is_active'  => true,
            ],
            [
                'kelurahan'  => 'Kelurahan Pasirjaya',
                'kecamatan'  => 'Sorean',
                'hari'       => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka'   => '08:00',
                'jam_tutup'  => '15:30',
                'istirahat'  => '12:00 - 13:00',
                'petugas'    => 'Bpk. Yusuf Prasetyo',
                'telepon'    => '(0251) 555-0105',
                'keterangan' => 'Pelayanan administrasi umum, SKTM, dan pengaduan warga.',
                'is_active'  => true,
            ],
        ];

        foreach ($jadwal as $item) {
            JadwalPelayanan::updateOrCreate(
                ['kelurahan' => $item['kelurahan'], 'kecamatan' => $item['kecamatan']],
                $item
            );
        }
    }
}
