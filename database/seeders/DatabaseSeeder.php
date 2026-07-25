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
            InstansiSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            JenisSuratSeeder::class,
            KategoriPengaduanSeeder::class,
            ExtraMenuSeeder::class,
            S3MenuSeeder::class,
        ]);
    }
}
