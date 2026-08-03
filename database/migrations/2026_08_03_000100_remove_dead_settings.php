<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hapus settings yang tidak dikonsumsi view/service mana pun:
     * - profile_map_embed, profile_kode_wilayah, app_keywords: tanpa konsumen tersisa
     * - contact_email: duplikat profile_email
     * - theme_color: selalu kalah dari cookie template customizer
     * SettingSeeder tidak lagi memuat key ini, tapi baris lama di production
     * hanya bisa dibersihkan lewat migration (seeder tidak menghapus).
     */
    public function up(): void
    {
        DB::table('settings')->whereIn('key', [
            'profile_map_embed',
            'profile_kode_wilayah',
            'contact_email',
            'app_keywords',
            'theme_color',
        ])->delete();
    }

    public function down(): void
    {
        //
    }
};
