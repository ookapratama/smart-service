<?php

namespace Database\Seeders;

use App\Models\KategoriPengaduan;
use Illuminate\Database\Seeder;

class KategoriPengaduanSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            'Infrastruktur',
            'Kebersihan',
            'Keamanan & Ketertiban',
            'Pelayanan Publik',
            'Lainnya',
        ];

        foreach ($kategoris as $nama) {
            KategoriPengaduan::updateOrCreate(['nama' => $nama], ['is_active' => true]);
        }
    }
}
