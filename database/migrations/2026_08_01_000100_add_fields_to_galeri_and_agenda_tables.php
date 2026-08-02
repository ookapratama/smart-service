<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('galeri', function (Blueprint $table) {
            if (!Schema::hasColumn('galeri', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('judul');
            }
            if (!Schema::hasColumn('galeri', 'kategori')) {
                $table->string('kategori')->default('Kegiatan')->after('slug');
            }
            if (!Schema::hasColumn('galeri', 'gambar')) {
                $table->string('gambar')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('galeri', 'tipe')) {
                $table->string('tipe')->default('foto')->after('gambar'); // foto / video
            }
            if (!Schema::hasColumn('galeri', 'video_url')) {
                $table->string('video_url')->nullable()->after('tipe');
            }
            if (!Schema::hasColumn('galeri', 'tgl_kegiatan')) {
                $table->date('tgl_kegiatan')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('galeri', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('tgl_kegiatan');
            }
        });

        Schema::table('agenda', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('judul');
            }
            if (!Schema::hasColumn('agenda', 'kategori')) {
                $table->string('kategori')->default('Kegiatan')->after('slug');
            }
            if (!Schema::hasColumn('agenda', 'penyelenggara')) {
                $table->string('penyelenggara')->default('Kecamatan Soreang')->after('kategori');
            }
            if (!Schema::hasColumn('agenda', 'waktu_keterangan')) {
                $table->string('waktu_keterangan')->nullable()->after('selesai_at');
            }
            if (!Schema::hasColumn('agenda', 'ringkasan')) {
                $table->text('ringkasan')->nullable()->after('waktu_keterangan');
            }
            if (!Schema::hasColumn('agenda', 'gambar')) {
                $table->string('gambar')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('agenda', 'file_lampiran')) {
                $table->string('file_lampiran')->nullable()->after('gambar');
            }
            if (!Schema::hasColumn('agenda', 'status_agenda')) {
                $table->string('status_agenda')->default('mendatang')->after('file_lampiran'); // mendatang / berlangsung / selesai
            }
            if (!Schema::hasColumn('agenda', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('status_agenda');
            }
        });
    }

    public function down(): void
    {
        Schema::table('galeri', function (Blueprint $table) {
            $columns = ['slug', 'kategori', 'gambar', 'tipe', 'video_url', 'tgl_kegiatan', 'views'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('galeri', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('agenda', function (Blueprint $table) {
            $columns = ['slug', 'kategori', 'penyelenggara', 'waktu_keterangan', 'ringkasan', 'gambar', 'file_lampiran', 'status_agenda', 'views'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('agenda', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
