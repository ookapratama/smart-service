<?php

use App\Models\JenisSurat;
use App\Models\KategoriPengaduan;
use App\Models\Pemohon;
use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Models\Role;
use App\Models\Tiket;
use App\Models\User;
use App\Services\SuratService;
use App\Services\TiketService;
use Illuminate\Support\Facades\Storage;

function buatWargaDenganPemohon(string $nik): array
{
    $role = Role::firstOrCreate(['slug' => 'warga'], ['name' => 'Warga']);
    $user = User::factory()->create(['role_id' => $role->id, 'password' => null]);

    $pemohon = Pemohon::create([
        'nik' => $nik,
        'name' => 'Warga Tiket '.substr($nik, -4),
        'phone' => '0813'.substr($nik, -8),
        'user_id' => $user->id,
    ]);

    return [$user, $pemohon];
}

function tiketSuratMilik(Pemohon $pemohon, string $status = 'diproses'): Tiket
{
    $jenis = JenisSurat::firstOrCreate(
        ['kode' => 'SKD-TS'],
        ['nama' => 'Surat Tiket Saya', 'fields' => [], 'is_active' => true]
    );

    $pengajuan = PengajuanSurat::create([
        'jenis_surat_id' => $jenis->id,
        'keperluan' => 'Keperluan tiket saya',
        'data' => [],
    ]);

    return $pengajuan->tiket()->create([
        'nomor_tiket' => 'TSS-2608-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Pengajuan Surat Tiket Saya',
        'status' => $status,
        'channel' => 'web',
    ]);
}

function tiketAduanMilik(Pemohon $pemohon, string $status = 'baru'): Tiket
{
    $kategori = KategoriPengaduan::firstOrCreate(['nama' => 'Kategori Tiket Saya']);

    $pengaduan = Pengaduan::create([
        'kategori_pengaduan_id' => $kategori->id,
        'deskripsi' => 'Aduan tiket saya',
        'lokasi' => 'Jl. Tiket Saya No. 1',
    ]);

    return $pengaduan->tiket()->create([
        'nomor_tiket' => 'TSA-2608-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Pengaduan Tiket Saya',
        'status' => $status,
        'channel' => 'web',
    ]);
}

beforeEach(function () {
    Storage::fake(SuratService::PDF_DISK);
});

test('should redirect guest to warga login when opening tiket saya', function () {
    $this->get(route('tiket-saya.index'))
        ->assertRedirect(route('warga.login'));
});

test('should list only own tickets on tiket saya index', function () {
    [$user, $pemohon] = buatWargaDenganPemohon('7371015566770001');
    [, $pemohonLain] = buatWargaDenganPemohon('7371015566770002');

    $milikSendiri = tiketAduanMilik($pemohon);
    $milikOrang = tiketAduanMilik($pemohonLain);

    $this->actingAs($user)
        ->get(route('tiket-saya.index'))
        ->assertOk()
        ->assertSee($milikSendiri->nomor_tiket)
        ->assertDontSee($milikOrang->nomor_tiket);
});

test('should return 404 when opening another pemohon ticket detail', function () {
    [$user] = buatWargaDenganPemohon('7371015566770001');
    [, $pemohonLain] = buatWargaDenganPemohon('7371015566770002');

    $milikOrang = tiketAduanMilik($pemohonLain);

    $this->actingAs($user)
        ->get(route('tiket-saya.show', $milikOrang->nomor_tiket))
        ->assertNotFound();
});

test('should download own finished surat pdf from tiket saya', function () {
    [$user, $pemohon] = buatWargaDenganPemohon('7371015566770001');
    $tiket = tiketSuratMilik($pemohon, 'diproses');

    // Selesaikan lewat service supaya nomor surat + PDF terbit seperti alur asli.
    app(TiketService::class)->updateStatus($tiket->id, 'selesai');

    $this->actingAs($user)
        ->get(route('tiket-saya.unduh', $tiket->nomor_tiket))
        ->assertOk();
});

test('should return 404 when downloading surat for pengaduan or unfinished ticket', function () {
    [$user, $pemohon] = buatWargaDenganPemohon('7371015566770001');

    $aduan = tiketAduanMilik($pemohon);
    $suratBelumSelesai = tiketSuratMilik($pemohon, 'diproses');

    $this->actingAs($user)
        ->get(route('tiket-saya.unduh', $aduan->nomor_tiket))
        ->assertNotFound();

    $this->actingAs($user)
        ->get(route('tiket-saya.unduh', $suratBelumSelesai->nomor_tiket))
        ->assertNotFound();
});

test('should redirect staff user from tiket saya to dashboard', function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get(route('tiket-saya.index'))
        ->assertRedirect(route('dashboard'));
});

test('should show status timeline and detail on own ticket', function () {
    [$user, $pemohon] = buatWargaDenganPemohon('7371015566770001');
    $tiket = tiketAduanMilik($pemohon);

    $tiket->statusLogs()->create([
        'status_from' => 'baru',
        'status_to' => 'baru',
        'user_id' => null,
        'catatan' => 'Pengaduan diterima via portal.',
    ]);

    $this->actingAs($user)
        ->get(route('tiket-saya.show', $tiket->nomor_tiket))
        ->assertOk()
        ->assertSee($tiket->nomor_tiket)
        ->assertSee('Pengaduan diterima via portal.');
});
