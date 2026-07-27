<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan');
            $table->string('kecamatan')->default('Sorean');
            $table->json('hari');          // e.g. ["Senin","Selasa","Rabu","Kamis","Jumat"]
            $table->string('jam_buka')->default('08:00');
            $table->string('jam_tutup')->default('15:00');
            $table->string('istirahat')->nullable();  // e.g. "12:00-13:00"
            $table->string('petugas')->nullable();
            $table->string('telepon')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelayanan');
    }
};
