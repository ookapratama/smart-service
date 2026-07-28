<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeri', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            // gambar disimpan via tabel media (morph 'mediable'), bukan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri');
    }
};
