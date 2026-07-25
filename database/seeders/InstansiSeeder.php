<?php

namespace Database\Seeders;

use App\Enums\InstansiLevel;
use App\Models\Instansi;
use Illuminate\Database\Seeder;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        $kabupaten = Instansi::updateOrCreate(
            ['kode' => 'BDG'],
            [
                'parent_id' => null,
                'name' => 'Kabupaten Bandung',
                'level' => InstansiLevel::Kabupaten,
                'is_active' => true,
            ]
        );

        $kecamatan = Instansi::updateOrCreate(
            ['kode' => 'SRG'],
            [
                'parent_id' => $kabupaten->id,
                'name' => 'Kecamatan Soreang',
                'level' => InstansiLevel::Kecamatan,
                'is_active' => true,
            ]
        );

        $kelurahan = [
            ['kode' => 'SOR', 'name' => 'Desa Soreang'],
            ['kode' => 'PMK', 'name' => 'Desa Pamekaran'],
            ['kode' => 'CCN', 'name' => 'Desa Cingcin'],
        ];

        foreach ($kelurahan as $desa) {
            Instansi::updateOrCreate(
                ['kode' => $desa['kode']],
                [
                    'parent_id' => $kecamatan->id,
                    'name' => $desa['name'],
                    'level' => InstansiLevel::Kelurahan,
                    'is_active' => true,
                ]
            );
        }
    }
}
