<?php

use App\Models\Instansi;
use App\Models\JenisSurat;
use App\Models\Pemohon;
use App\Models\PengajuanSurat;
use App\Models\Tiket;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

test('new s3 tables and columns exist', function () {
    expect(Schema::hasTable('instansi'))->toBeTrue();
    expect(Schema::hasTable('pemohon'))->toBeTrue();
    expect(Schema::hasTable('jenis_surat'))->toBeTrue();
    expect(Schema::hasTable('kategori_pengaduan'))->toBeTrue();
    expect(Schema::hasTable('tiket'))->toBeTrue();
    expect(Schema::hasTable('status_log'))->toBeTrue();
    expect(Schema::hasTable('pengajuan_surat'))->toBeTrue();
    expect(Schema::hasTable('pengaduan'))->toBeTrue();

    expect(Schema::hasColumns('users', ['instansi_id']))->toBeTrue();
    expect(Schema::hasColumns('settings', ['instansi_id']))->toBeTrue();
    expect(Schema::hasColumns('media', ['mediable_type', 'mediable_id']))->toBeTrue();
    expect(Schema::hasColumns('tiket', ['instansi_id', 'nomor_tiket', 'pemohon_id', 'detail_type', 'detail_id', 'status', 'channel']))->toBeTrue();
});

test('tenant scope filters tiket by currentInstansi and auto-fills instansi_id on create', function () {
    $instansiA = Instansi::create(['name' => 'Kelurahan A', 'level' => 'kelurahan', 'kode' => 'AAA']);
    $instansiB = Instansi::create(['name' => 'Kelurahan B', 'level' => 'kelurahan', 'kode' => 'BBB']);

    $pemohonA = Pemohon::create(['instansi_id' => $instansiA->id, 'nik' => '1111111111111111', 'name' => 'Warga A']);
    $pemohonB = Pemohon::create(['instansi_id' => $instansiB->id, 'nik' => '2222222222222222', 'name' => 'Warga B']);

    $jenis = JenisSurat::create(['kode' => 'TST', 'nama' => 'Test Surat']);

    $pengajuanA = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'test']);
    $pengajuanA->tiket()->create([
        'instansi_id' => $instansiA->id,
        'nomor_tiket' => 'AAA-2607-00001',
        'pemohon_id' => $pemohonA->id,
        'judul' => 'Tiket A',
        'status' => 'baru',
        'channel' => 'web',
    ]);

    $pengajuanB = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'test']);
    $pengajuanB->tiket()->create([
        'instansi_id' => $instansiB->id,
        'nomor_tiket' => 'BBB-2607-00001',
        'pemohon_id' => $pemohonB->id,
        'judul' => 'Tiket B',
        'status' => 'baru',
        'channel' => 'web',
    ]);

    app()->instance('currentInstansi', $instansiA);

    expect(Tiket::count())->toBe(1);
    expect(Tiket::first()->nomor_tiket)->toBe('AAA-2607-00001');
    expect(Tiket::withoutGlobalScope('instansi')->count())->toBe(2);

    $pengajuanC = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'auto']);
    $autoTiket = $pengajuanC->tiket()->create([
        'nomor_tiket' => 'AAA-2607-00002',
        'pemohon_id' => $pemohonA->id,
        'judul' => 'Tiket Auto',
        'status' => 'baru',
        'channel' => 'web',
    ]);

    expect($autoTiket->instansi_id)->toBe($instansiA->id);
});

test('tiket detail morph resolves via morph map', function () {
    $instansi = Instansi::create(['name' => 'Kelurahan C', 'level' => 'kelurahan', 'kode' => 'CCC']);
    $pemohon = Pemohon::create(['instansi_id' => $instansi->id, 'nik' => '3333333333333333', 'name' => 'Warga C']);
    $jenis = JenisSurat::create(['kode' => 'MRF', 'nama' => 'Morph Test']);
    $pengajuan = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'morph test']);

    $tiket = $pengajuan->tiket()->create([
        'instansi_id' => $instansi->id,
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

test('nik is unique per instansi but reusable across instansi', function () {
    $instansiA = Instansi::create(['name' => 'Kelurahan D', 'level' => 'kelurahan', 'kode' => 'DDD']);
    $instansiE = Instansi::create(['name' => 'Kelurahan E', 'level' => 'kelurahan', 'kode' => 'EEE']);

    Pemohon::create(['instansi_id' => $instansiA->id, 'nik' => '4444444444444444', 'name' => 'Warga D']);

    expect(fn () => Pemohon::create(['instansi_id' => $instansiA->id, 'nik' => '4444444444444444', 'name' => 'Dup']))
        ->toThrow(QueryException::class);

    $crossTenant = Pemohon::create(['instansi_id' => $instansiE->id, 'nik' => '4444444444444444', 'name' => 'Warga E']);
    expect($crossTenant->id)->not->toBeNull();
});

test('nomor tiket is unique per instansi', function () {
    $instansi = Instansi::create(['name' => 'Kelurahan F', 'level' => 'kelurahan', 'kode' => 'FFF']);
    $pemohon = Pemohon::create(['instansi_id' => $instansi->id, 'nik' => '5555555555555555', 'name' => 'Warga F']);
    $jenis = JenisSurat::create(['kode' => 'DUP', 'nama' => 'Dup Test']);

    $pengajuan1 = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'test']);
    $pengajuan1->tiket()->create([
        'instansi_id' => $instansi->id,
        'nomor_tiket' => 'FFF-2607-00001',
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket 1',
        'status' => 'baru',
        'channel' => 'web',
    ]);

    $pengajuan2 = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'test']);

    expect(fn () => $pengajuan2->tiket()->create([
        'instansi_id' => $instansi->id,
        'nomor_tiket' => 'FFF-2607-00001',
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Dup',
        'status' => 'baru',
        'channel' => 'web',
    ]))->toThrow(QueryException::class);
});
