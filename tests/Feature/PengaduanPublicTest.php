<?php

use App\Models\KategoriPengaduan;
use App\Models\NotifikasiWa;
use App\Models\Pemohon;
use App\Models\Tiket;
use App\Services\TiketService;

beforeEach(function () {
    $this->kategori = KategoriPengaduan::create(['nama' => 'Infrastruktur', 'is_active' => true]);
});

function pengaduanPayload(array $overrides = []): array
{
    return array_merge([
        'nama' => 'Warga Uji',
        'nik' => '3204011234567890',
        'email' => 'warga@example.com',
        'phone' => '081234567890',
        'jenis_laporan' => 'Pengaduan / Keluhan',
        'judul' => 'Jalan Rusak',
        'lokasi' => 'Jl. Uji No. 1',
        'deskripsi' => 'Jalan berlubang cukup dalam dan membahayakan pengendara.',
        'is_anonim' => '0',
    ], $overrides);
}

test('guest can submit a pengaduan and gets a sequential nomor tiket', function () {
    $payload = pengaduanPayload(['kategori_pengaduan_id' => $this->kategori->id]);

    $response = $this->post(route('pengaduan.store'), $payload);

    $periode = now()->format('ym');
    $nomor = "SRG-{$periode}-00001";
    $response->assertRedirect(route('pengaduan.sukses', $nomor));

    $tiket = Tiket::where('nomor_tiket', $nomor)->firstOrFail();
    expect($tiket->detail_type)->toBe('pengaduan');
    expect($tiket->detail->is_anonim)->toBeFalse();
    expect($tiket->pemohon->name)->toBe('Warga Uji');
    expect($tiket->statusLogs()->count())->toBe(1);

    $second = $this->post(route('pengaduan.store'), array_merge($payload, [
        'nik' => '3204019876543210',
        'kategori_pengaduan_id' => $this->kategori->id,
    ]));
    $second->assertRedirect(route('pengaduan.sukses', "SRG-{$periode}-00002"));
});

test('anonim flag hides identity from officers but keeps real name stored', function () {
    $payload = pengaduanPayload([
        'kategori_pengaduan_id' => $this->kategori->id,
        'is_anonim' => '1',
    ]);

    $this->post(route('pengaduan.store'), $payload);

    $pemohon = Pemohon::where('nik', '3204011234567890')->firstOrFail();
    expect($pemohon->name)->toBe('Warga Uji');

    $tiket = Tiket::where('pemohon_id', $pemohon->id)->firstOrFail();
    expect($tiket->detail->is_anonim)->toBeTrue();
});

test('nik must be exactly 16 digits', function () {
    $payload = pengaduanPayload([
        'kategori_pengaduan_id' => $this->kategori->id,
        'nik' => 'abc123',
    ]);

    $this->post(route('pengaduan.store'), $payload)->assertSessionHasErrors('nik');
});

test('cek status api finds tiket by nomor or nik', function () {
    $this->post(route('pengaduan.store'), pengaduanPayload(['kategori_pengaduan_id' => $this->kategori->id]));

    $periode = now()->format('ym');
    $nomor = "SRG-{$periode}-00001";

    $byNomor = $this->postJson('/api/cek-status', ['keyword' => $nomor]);
    $byNomor->assertOk()->assertJsonPath('data.nomor_tiket', $nomor);

    $byNik = $this->postJson('/api/cek-status', ['keyword' => '3204011234567890']);
    $byNik->assertOk()->assertJsonPath('data.nomor_tiket', $nomor);
});

test('cek nik endpoint returns correct status for existing and non-existing pemohon', function () {
    Pemohon::create([
        'nik' => '3204011111222233',
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '081299998888',
    ]);

    $existsRes = $this->postJson(route('pengaduan.cek-nik'), ['nik' => '3204011111222233']);
    $existsRes->assertOk()
        ->assertJsonPath('exists', true)
        ->assertJsonPath('pemohon.name', 'Budi Santoso');

    $notExistsRes = $this->postJson(route('pengaduan.cek-nik'), ['nik' => '3204019999999999']);
    $notExistsRes->assertOk()
        ->assertJsonPath('exists', false);
});

test('submitting pengaduan sends wa notification and logs to notifikasi_wa', function () {
    $payload = pengaduanPayload(['kategori_pengaduan_id' => $this->kategori->id]);
    $this->post(route('pengaduan.store'), $payload);

    $notif = NotifikasiWa::latest()->first();
    expect($notif)->not->toBeNull();
    expect($notif->phone)->toBe('081234567890');
    expect($notif->pesan)->toContain('Laporan pengaduan Anda telah kami terima');
});

test('officer updating status sends wa notification with catatan', function () {
    $payload = pengaduanPayload(['kategori_pengaduan_id' => $this->kategori->id]);
    $this->post(route('pengaduan.store'), $payload);

    $tiket = Tiket::firstOrFail();

    $tiketService = app(TiketService::class);
    $tiketService->updateStatus($tiket->id, 'diproses', 'Petugas sedang menuju lokasi.');

    $latestNotif = NotifikasiWa::orderByDesc('id')->first();
    expect($latestNotif)->not->toBeNull();
    expect($latestNotif->pesan)->toContain('telah diperbarui menjadi [Diproses]');
    expect($latestNotif->pesan)->toContain('Petugas sedang menuju lokasi.');
});
