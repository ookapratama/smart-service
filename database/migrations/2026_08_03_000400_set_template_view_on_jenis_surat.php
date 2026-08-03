<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration baris production: isi template_view per kategori surat
     * (JenisSuratSeeder kini insert-only). Hanya baris yang masih null —
     * pilihan admin lewat form tidak pernah ditimpa. Idempotent.
     */
    public function up(): void
    {
        $map = [
            'skd' => ['SKD'],
            'keterangan' => ['SKTM', 'SKU', 'SKBM', 'SKKL', 'SKKM', 'SKP'],
            'pengantar' => ['SPKTP', 'SPKK', 'SPSKCK'],
        ];

        foreach ($map as $template => $kodes) {
            DB::table('jenis_surat')
                ->whereIn('kode', $kodes)
                ->whereNull('template_view')
                ->update(['template_view' => $template, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        //
    }
};
