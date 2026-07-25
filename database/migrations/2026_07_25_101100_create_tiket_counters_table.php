<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instansi_id')->constrained('instansi')->cascadeOnDelete();
            $table->char('periode', 4);
            $table->unsignedInteger('last_seq')->default(0);
            $table->timestamps();

            $table->unique(['instansi_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_counters');
    }
};
