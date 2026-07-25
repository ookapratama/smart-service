<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket')->cascadeOnDelete();
            $table->string('status_from')->nullable();
            $table->string('status_to');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['tiket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_log');
    }
};
