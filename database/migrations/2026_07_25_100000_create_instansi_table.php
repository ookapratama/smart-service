<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('instansi')->nullOnDelete();
            $table->string('name');
            $table->string('level');
            $table->string('kode', 10)->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instansi');
    }
};
