<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration untuk baris jenis_surat yang SUDAH ada di production:
     * JenisSuratSeeder kini insert-only (no-clobber), jadi penambahan field
     * lampiran pengantar RT/RW ke baris lama harus lewat migration.
     * Idempotent: hanya menambah bila belum ada field bernama pengantar_rt_rw.
     */
    public function up(): void
    {
        $rows = DB::table('jenis_surat')->where('wajib_pengantar_rt_rw', true)->get(['id', 'fields']);

        foreach ($rows as $row) {
            $fields = json_decode($row->fields ?? '[]', true) ?: [];

            $sudahAda = collect($fields)->contains(fn ($field) => ($field['name'] ?? null) === 'pengantar_rt_rw');

            if ($sudahAda) {
                continue;
            }

            $fields[] = [
                'name' => 'pengantar_rt_rw',
                'type' => 'file',
                'label' => 'Scan Surat Pengantar RT/RW',
                'required' => true,
            ];

            DB::table('jenis_surat')->where('id', $row->id)->update([
                'fields' => json_encode($fields),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
