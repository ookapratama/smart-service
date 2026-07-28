<?php

namespace Database\Seeders;

use App\Models\Kelurahan;
use Illuminate\Database\Seeder;

class KelurahanSeeder extends Seeder
{
    public function run(): void
    {
        $kelurahan = [
            ['kode' => 'BHR', 'nama' => 'Bukit Harapan'],
            ['kode' => 'BID', 'nama' => 'Bukit Indah'],
            ['kode' => 'KPS', 'nama' => 'Kampung Pisang'],
            ['kode' => 'LKS', 'nama' => 'Lakessi'],
            ['kode' => 'UBR', 'nama' => 'Ujung Baru'],
            ['kode' => 'ULR', 'nama' => 'Ujung Lare'],
            ['kode' => 'WSR', 'nama' => 'Watang Soreang'],
        ];

        foreach ($kelurahan as $item) {
            Kelurahan::updateOrCreate(
                ['kode' => $item['kode']],
                ['nama' => $item['nama'], 'is_active' => true]
            );
        }
    }
}
