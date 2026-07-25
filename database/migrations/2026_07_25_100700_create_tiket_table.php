<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instansi_id')->constrained('instansi')->cascadeOnDelete();
            $table->string('nomor_tiket', 30);
            $table->foreignId('pemohon_id')->constrained('pemohon')->cascadeOnDelete();
            $table->morphs('detail');
            $table->string('status')->default('baru');
            $table->string('channel')->default('web');
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->unique(['instansi_id', 'nomor_tiket']);
            $table->index(['instansi_id', 'status', 'created_at']);
            $table->index(['pemohon_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket');
    }
};
