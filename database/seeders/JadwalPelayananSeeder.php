<?php

namespace Database\Seeders;

use App\Models\JadwalPelayanan;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;

class JadwalPelayananSeeder extends Seeder
{
    public function run(): void
    {
        $jadwal = [
            [
                'kode_kelurahan' => 'BHR',
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka' => '08:00',
                'jam_tutup' => '15:00',
                'istirahat' => '12:00 - 13:00',
                'petugas' => 'Bpk. Ahmad Ridwan',
                'telepon' => '(0421) 21101',
                'keterangan' => 'Pelayanan administrasi umum dan surat-menyurat.',
                'is_active' => true,
            ],
            [
                'kode_kelurahan' => 'BID',
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka' => '08:00',
                'jam_tutup' => '14:30',
                'istirahat' => '12:00 - 13:00',
                'petugas' => 'Ibu Sari Dewi',
                'telepon' => '(0421) 21102',
                'keterangan' => 'Pelayanan kependudukan dan administrasi warga.',
                'is_active' => true,
            ],
            [
                'kode_kelurahan' => 'KPS',
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka' => '07:30',
                'jam_tutup' => '15:00',
                'istirahat' => '12:00 - 13:00',
                'petugas' => 'Bpk. Doni Firmansyah',
                'telepon' => '(0421) 21103',
                'keterangan' => 'Melayani surat pengantar, keterangan domisili, dan layanan UMKM.',
                'is_active' => true,
            ],
            [
                'kode_kelurahan' => 'LKS',
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka' => '08:00',
                'jam_tutup' => '15:00',
                'istirahat' => '11:30 - 12:30',
                'petugas' => 'Ibu Rina Hastuti',
                'telepon' => '(0421) 21104',
                'keterangan' => 'Pelayanan surat kematian, kelahiran, dan pengantar nikah.',
                'is_active' => true,
            ],
            [
                'kode_kelurahan' => 'UBR',
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka' => '08:00',
                'jam_tutup' => '15:30',
                'istirahat' => '12:00 - 13:00',
                'petugas' => 'Bpk. Yusuf Prasetyo',
                'telepon' => '(0421) 21105',
                'keterangan' => 'Pelayanan administrasi umum, SKTM, dan pengaduan warga.',
                'is_active' => true,
            ],
            [
                'kode_kelurahan' => 'ULR',
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka' => '08:00',
                'jam_tutup' => '15:00',
                'istirahat' => '12:00 - 13:00',
                'petugas' => 'Ibu Nur Aisyah',
                'telepon' => '(0421) 21106',
                'keterangan' => 'Pelayanan surat pengantar KTP/KK dan keterangan usaha.',
                'is_active' => true,
            ],
            [
                'kode_kelurahan' => 'WSR',
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                'jam_buka' => '08:00',
                'jam_tutup' => '15:00',
                'istirahat' => '12:00 - 13:00',
                'petugas' => 'Bpk. Andi Muslimin',
                'telepon' => '(0421) 21107',
                'keterangan' => 'Pelayanan administrasi umum dan pengaduan masyarakat.',
                'is_active' => true,
            ],
        ];

        foreach ($jadwal as $item) {
            $kelurahan = Kelurahan::where('kode', $item['kode_kelurahan'])->first();

            if (! $kelurahan) {
                continue;
            }

            unset($item['kode_kelurahan']);

            JadwalPelayanan::updateOrCreate(
                ['kelurahan_id' => $kelurahan->id],
                $item
            );
        }
    }
}
