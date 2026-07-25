<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('instansi_id')->nullable()->after('id')
                ->constrained('instansi')->cascadeOnDelete();
            $table->dropUnique('settings_key_unique');
            $table->unique(['instansi_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['instansi_id']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['instansi_id', 'key']);
            $table->dropColumn('instansi_id');
            $table->unique('key');
        });
    }
};
