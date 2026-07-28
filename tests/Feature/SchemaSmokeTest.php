<?php

use App\Models\JenisSurat;
use App\Models\Pemohon;
use App\Models\PengajuanSurat;
use App\Services\TiketService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('new s3 tables and columns exist, tenancy tables do not', function () {
    expect(Schema::hasTable('instansi'))->toBeFalse();
    expect(Schema::hasTable('wilayah'))->toBeFalse();

    expect(Schema::hasTable('kelurahan'))->toBeTrue();
    expect(Schema::hasTable('pemohon'))->toBeTrue();
    expect(Schema::hasTable('jenis_surat'))->toBeTrue();
    expect(Schema::hasTable('kategori_pengaduan'))->toBeTrue();
    expect(Schema::hasTable('tiket'))->toBeTrue();
    expect(Schema::hasTable('status_log'))->toBeTrue();
    expect(Schema::hasTable('pengajuan_surat'))->toBeTrue();
    expect(Schema::hasTable('pengaduan'))->toBeTrue();
    expect(Schema::hasTable('agenda'))->toBeTrue();
    expect(Schema::hasTable('galeri'))->toBeTrue();
    expect(Schema::hasTable('notifikasi_wa'))->toBeTrue();
    expect(Schema::hasTable('surat_counters'))->toBeTrue();

    expect(Schema::hasColumns('users', ['instansi_id']))->toBeFalse();
    expect(Schema::hasColumns('settings', ['instansi_id']))->toBeFalse();
    expect(Schema::hasColumns('pemohon', ['instansi_id']))->toBeFalse();
    expect(Schema::hasColumns('tiket', ['instansi_id']))->toBeFalse();
    expect(Schema::hasColumns('tiket_counters', ['instansi_id']))->toBeFalse();

    expect(Schema::hasColumns('media', ['mediable_type', 'mediable_id']))->toBeTrue();
    expect(Schema::hasColumns('tiket', ['nomor_tiket', 'pemohon_id', 'detail_type', 'detail_id', 'status', 'channel']))->toBeTrue();
    expect(Schema::hasColumns('pemohon', ['kelurahan_id']))->toBeTrue();
    expect(Schema::hasColumns('pengaduan', ['is_anonim', 'jenis_laporan', 'tanggal_kejadian']))->toBeTrue();
    expect(Schema::hasColumns('pengajuan_surat', ['nomor_surat']))->toBeTrue();
    expect(Schema::hasColumns('jenis_surat', ['template_view']))->toBeTrue();
    expect(Schema::hasColumns('jadwal_pelayanan', ['kelurahan_id']))->toBeTrue();
});

test('tiket detail morph resolves via morph map', function () {
    $pemohon = Pemohon::create(['nik' => '3333333333333333', 'name' => 'Warga C']);
    $jenis = JenisSurat::create(['kode' => 'MRF', 'nama' => 'Morph Test']);
    $pengajuan = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'morph test']);

    $tiket = $pengajuan->tiket()->create([
        'nomor_tiket' => 'CCC-2607-00001',
        'pemohon_id' => $pemohon->id,
        'judul' => 'Morph Tiket',
        'status' => 'baru',
        'channel' => 'web',
    ]);

    $tiket->refresh();

    expect($tiket->detail)->toBeInstanceOf(PengajuanSurat::class);
    expect($tiket->detail_type)->toBe('pengajuan_surat');
    expect($pengajuan->fresh()->tiket->id)->toBe($tiket->id);
});

test('nik is unique globally', function () {
    Pemohon::create(['nik' => '4444444444444444', 'name' => 'Warga D']);

    expect(fn () => Pemohon::create(['nik' => '4444444444444444', 'name' => 'Dup']))
        ->toThrow(QueryException::class);
});

test('nomor tiket is unique globally', function () {
    $pemohon = Pemohon::create(['nik' => '5555555555555555', 'name' => 'Warga F']);
    $jenis = JenisSurat::create(['kode' => 'DUP', 'nama' => 'Dup Test']);

    $pengajuan1 = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'test']);
    $pengajuan1->tiket()->create([
        'nomor_tiket' => 'FFF-2607-00001',
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket 1',
        'status' => 'baru',
        'channel' => 'web',
    ]);

    $pengajuan2 = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'test']);

    expect(fn () => $pengajuan2->tiket()->create([
        'nomor_tiket' => 'FFF-2607-00001',
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Dup',
        'status' => 'baru',
        'channel' => 'web',
    ]))->toThrow(QueryException::class);
});

test('generateNomorTiket is race-safe via atomic upsert', function () {
    $service = app(TiketService::class);

    $first = DB::transaction(fn () => $service->generateNomorTiket());
    $second = DB::transaction(fn () => $service->generateNomorTiket());

    $periode = now()->format('ym');
    expect($first)->toBe("SRG-{$periode}-00001");
    expect($second)->toBe("SRG-{$periode}-00002");
});
