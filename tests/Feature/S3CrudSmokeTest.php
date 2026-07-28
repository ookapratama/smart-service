<?php

use App\Models\JenisSurat;
use App\Models\KategoriPengaduan;
use App\Models\Pemohon;
use App\Models\PengajuanSurat;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
});

test('admin can view jenis surat and kategori pengaduan pages', function () {
    $jenis = JenisSurat::create(['kode' => 'TST', 'nama' => 'Surat Uji']);
    $kategori = KategoriPengaduan::create(['nama' => 'Kategori Uji']);

    $this->actingAs($this->admin)->get(route('jenis-surat.index'))->assertOk();
    $this->actingAs($this->admin)->get(route('jenis-surat.show', $jenis->id))->assertOk();
    $this->actingAs($this->admin)->get(route('kategori-pengaduan.index'))->assertOk();
    $this->actingAs($this->admin)->get(route('kategori-pengaduan.show', $kategori->id))->assertOk();
});

test('admin can view and create pemohon', function () {
    $this->actingAs($this->admin)->get(route('pemohon.index'))->assertOk();
    $this->actingAs($this->admin)->get(route('pemohon.create'))->assertOk();

    $this->actingAs($this->admin)->post(route('pemohon.store'), [
        'nik' => '3204011234567890',
        'name' => 'Warga Uji',
    ])->assertRedirect(route('pemohon.index'));

    $this->assertDatabaseHas('pemohon', ['nik' => '3204011234567890']);
});

test('admin can view tiket queue and transition its status with a status_log entry', function () {
    $pemohon = Pemohon::create(['nik' => '3204019999999999', 'name' => 'Warga Tiket']);
    $jenis = JenisSurat::create(['kode' => 'TKT', 'nama' => 'Surat Tiket']);
    $pengajuan = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'uji tiket']);

    $tiket = $pengajuan->tiket()->create([
        'nomor_tiket' => 'UJI-2607-00001',
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Uji',
        'status' => 'baru',
        'channel' => 'web',
    ]);

    $this->actingAs($this->admin)->get(route('tiket.index'))->assertOk();
    $this->actingAs($this->admin)->get(route('tiket.show', $tiket->id))->assertOk();

    $this->actingAs($this->admin)->post(route('tiket.update-status', $tiket->id), [
        'status_to' => 'diproses',
        'catatan' => 'Sedang diverifikasi',
    ])->assertRedirect(route('tiket.show', $tiket->id));

    $this->assertDatabaseHas('tiket', ['id' => $tiket->id, 'status' => 'diproses']);
    $this->assertDatabaseHas('status_log', [
        'tiket_id' => $tiket->id,
        'status_from' => 'baru',
        'status_to' => 'diproses',
        'catatan' => 'Sedang diverifikasi',
    ]);
});

test('invalid status transition is rejected', function () {
    $pemohon = Pemohon::create(['nik' => '3204018888888888', 'name' => 'Warga Tolak']);
    $jenis = JenisSurat::create(['kode' => 'RJT', 'nama' => 'Surat Reject']);
    $pengajuan = PengajuanSurat::create(['jenis_surat_id' => $jenis->id, 'keperluan' => 'uji reject']);

    $tiket = $pengajuan->tiket()->create([
        'nomor_tiket' => 'UJI-2607-00002',
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Selesai',
        'status' => 'selesai',
        'channel' => 'web',
    ]);

    $this->actingAs($this->admin)->post(route('tiket.update-status', $tiket->id), [
        'status_to' => 'diproses',
    ])->assertRedirect();

    $this->assertDatabaseHas('tiket', ['id' => $tiket->id, 'status' => 'selesai']);
});
