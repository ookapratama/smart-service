<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndMenuSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            KelurahanSeeder::class,
            JenisSuratSeeder::class,
            KategoriPengaduanSeeder::class,
            BeritaSeeder::class,
            JadwalPelayananSeeder::class,
            ExtraMenuSeeder::class,
            S3MenuSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
