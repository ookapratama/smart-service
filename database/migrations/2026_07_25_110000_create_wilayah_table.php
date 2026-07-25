<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('parent_code', 20)->nullable();
            $table->string('name');
            $table->unsignedTinyInteger('level');
            $table->timestamps();

            $table->foreign('parent_code')->references('code')->on('wilayah')->cascadeOnDelete();
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
