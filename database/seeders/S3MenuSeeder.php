<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Seeder;

class S3MenuSeeder extends Seeder
{
    public function run(): void
    {
        $fullCrud = ['can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => true];
        $noDelete = ['can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => false];
        $readUpdateOnly = ['can_create' => false, 'can_read' => true, 'can_update' => true, 'can_delete' => false];
        $readOnly = ['can_create' => false, 'can_read' => true, 'can_update' => false, 'can_delete' => false];

        // Top-level: Manajemen Instansi
        $instansiMenu = Menu::updateOrCreate(
            ['slug' => 'instansi.index'],
            ['name' => 'Manajemen Instansi', 'icon' => 'ri-building-4-line', 'path' => 'instansi', 'parent_id' => null, 'order_no' => 10]
        );

        // Master Data group
        $masterDataMenu = Menu::updateOrCreate(
            ['slug' => 'master-data'],
            ['name' => 'Master Data', 'icon' => 'ri-database-2-line', 'path' => null, 'parent_id' => null, 'order_no' => 11]
        );
        $jenisSuratMenu = Menu::updateOrCreate(
            ['slug' => 'jenis-surat.index'],
            ['name' => 'Jenis Surat', 'icon' => 'ri-file-text-line', 'path' => 'jenis-surat', 'parent_id' => $masterDataMenu->id, 'order_no' => 1]
        );
        $kategoriPengaduanMenu = Menu::updateOrCreate(
            ['slug' => 'kategori-pengaduan.index'],
            ['name' => 'Kategori Pengaduan', 'icon' => 'ri-price-tag-3-line', 'path' => 'kategori-pengaduan', 'parent_id' => $masterDataMenu->id, 'order_no' => 2]
        );

        // Pelayanan group
        $pelayananMenu = Menu::updateOrCreate(
            ['slug' => 'pelayanan'],
            ['name' => 'Pelayanan', 'icon' => 'ri-service-line', 'path' => null, 'parent_id' => null, 'order_no' => 12]
        );
        $tiketMenu = Menu::updateOrCreate(
            ['slug' => 'tiket.index'],
            ['name' => 'Tiket', 'icon' => 'ri-ticket-2-line', 'path' => 'tiket', 'parent_id' => $pelayananMenu->id, 'order_no' => 1]
        );
        $pemohonMenu = Menu::updateOrCreate(
            ['slug' => 'pemohon.index'],
            ['name' => 'Data Pemohon', 'icon' => 'ri-user-follow-line', 'path' => 'pemohon', 'parent_id' => $pelayananMenu->id, 'order_no' => 2]
        );

        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $petugasInstansi = Role::where('slug', 'petugas-instansi')->first();

        if ($superAdmin) {
            $superAdmin->menus()->syncWithoutDetaching([
                $instansiMenu->id => $fullCrud,
                $masterDataMenu->id => $readOnly,
                $jenisSuratMenu->id => $fullCrud,
                $kategoriPengaduanMenu->id => $fullCrud,
                $pelayananMenu->id => $readOnly,
                $tiketMenu->id => $fullCrud,
                $pemohonMenu->id => $fullCrud,
            ]);
        }

        if ($admin) {
            $admin->menus()->syncWithoutDetaching([
                $instansiMenu->id => $noDelete,
                $masterDataMenu->id => $readOnly,
                $jenisSuratMenu->id => $noDelete,
                $kategoriPengaduanMenu->id => $noDelete,
                $pelayananMenu->id => $readOnly,
                $tiketMenu->id => $readUpdateOnly,
                $pemohonMenu->id => $noDelete,
            ]);
        }

        // Petugas Instansi: only Tiket (process) + Pemohon (register/manage), scoped to
        // their own instansi once tenant-resolution middleware exists — see REFACTOR_BACKLOG.md.
        // No access to Instansi/Master Data management (platform-level concerns).
        if ($petugasInstansi) {
            $petugasInstansi->menus()->syncWithoutDetaching([
                $pelayananMenu->id => $readOnly,
                $tiketMenu->id => $readUpdateOnly,
                $pemohonMenu->id => $noDelete,
            ]);
        }
    }
}
