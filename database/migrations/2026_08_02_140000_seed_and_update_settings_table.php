<?php

use Database\Seeders\SettingSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Fresh-install parity: delegasikan ke SettingSeeder (idempotent + value-preserving)
     * supaya daftar settings hanya hidup di SATU tempat. Di production migration ini
     * sudah tercatat berjalan, jadi edit ini hanya berpengaruh pada instalasi baru.
     */
    public function up(): void
    {
        (new SettingSeeder)->run();
    }

    public function down(): void
    {
        //
    }
};
