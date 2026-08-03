<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom dibuat varchar(255) sementara validasi mengizinkan max:500 —
     * di MySQL strict mode nilai 256-500 karakter melempar QueryException.
     * Dilebarkan mengikuti aturan validasi; tidak pernah dipersempit kembali.
     */
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->string('keperluan', 500)->change();
        });
    }

    public function down(): void
    {
        //
    }
};
