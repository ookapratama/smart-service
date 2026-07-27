<?php

namespace Database\Seeders;

use App\Models\Instansi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $petugasRole = Role::where('slug', 'petugas-instansi')->first();
        $userRole = Role::where('slug', 'user')->first();

        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
            ]
        );

        // 2. Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // 3. Regular User
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role_id' => $userRole->id,
            ]
        );

        // 4. Petugas Instansi — demo accounts tied to two different instansi levels
        if ($petugasRole) {
            $kecamatanSoreang = Instansi::where('kode', 'SRG')->first();
            $desaPamekaran = Instansi::where('kode', 'PMK')->first();

            if ($kecamatanSoreang) {
                User::updateOrCreate(
                    ['email' => 'petugas.soreang@gmail.com'],
                    [
                        'name' => 'Petugas Kecamatan Soreang',
                        'password' => Hash::make('password'),
                        'role_id' => $petugasRole->id,
                        'instansi_id' => $kecamatanSoreang->id,
                    ]
                );
            }

            if ($desaPamekaran) {
                User::updateOrCreate(
                    ['email' => 'petugas.pamekaran@gmail.com'],
                    [
                        'name' => 'Petugas Desa Pamekaran',
                        'password' => Hash::make('password'),
                        'role_id' => $petugasRole->id,
                        'instansi_id' => $desaPamekaran->id,
                    ]
                );
            }
        }

        $this->command->info('Users created with password: password');
    }
}
