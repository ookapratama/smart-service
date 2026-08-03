<?php

use App\Models\JenisSurat;
use App\Models\KategoriPengaduan;
use App\Models\Pemohon;
use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Models\Role;
use App\Models\Tiket;
use App\Models\User;

function antrianPemohon(string $nik, string $name = 'Warga Antrian'): Pemohon
{
    return Pemohon::firstOrCreate(
        ['nik' => $nik],
        ['name' => $name, 'phone' => '0814'.substr($nik, -8)]
    );
}

function antrianTiketSurat(Pemohon $pemohon, int $seq, ?int $jenisSuratId = null): void
{
    $jenisSuratId ??= JenisSurat::firstOrCreate(
        ['kode' => 'SKD-Q'],
        ['nama' => 'Surat Antrian', 'fields' => [], 'is_active' => true]
    )->id;

    $pengajuan = PengajuanSurat::create([
        'jenis_surat_id' => $jenisSuratId,
        'keperluan' => 'Antrian uji',
        'data' => [],
    ]);

    $pengajuan->tiket()->create([
        'nomor_tiket' => sprintf('ANQ-2608-%05d', $seq),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Antrian Surat '.$seq,
        'status' => 'baru',
        'channel' => 'web',
    ]);
}

function antrianTiketAduan(Pemohon $pemohon, int $seq): void
{
    $kategori = KategoriPengaduan::firstOrCreate(['nama' => 'Kategori Antrian']);

    $pengaduan = Pengaduan::create([
        'kategori_pengaduan_id' => $kategori->id,
        'deskripsi' => 'Aduan antrian '.$seq,
        'lokasi' => 'Jl. Antrian',
    ]);

    $pengaduan->tiket()->create([
        'nomor_tiket' => sprintf('ANA-2608-%05d', $seq),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Antrian Aduan '.$seq,
        'status' => 'baru',
        'channel' => 'web',
    ]);
}

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
});

test('should paginate tiket queue at 20 per page with continued numbering', function () {
    $pemohon = antrianPemohon('7371016600110001');

    foreach (range(1, 25) as $seq) {
        antrianTiketAduan($pemohon, $seq);
    }

    $page1 = $this->actingAs($this->admin)->get(route('tiket.index'));
    $page1->assertOk();
    expect($page1->viewData('data'))->toHaveCount(20);

    $page2 = $this->actingAs($this->admin)->get(route('tiket.index', ['page' => 2]));
    $page2->assertOk();
    expect($page2->viewData('data'))->toHaveCount(5)
        ->and($page2->viewData('data')->firstItem())->toBe(21);
});

test('should filter queue by search keyword on nomor tiket and pemohon nik', function () {
    $pemohonA = antrianPemohon('7371016600110001', 'Ahmad Pencarian');
    $pemohonB = antrianPemohon('7371016600220002', 'Budi Lainnya');

    antrianTiketAduan($pemohonA, 1);
    antrianTiketAduan($pemohonB, 2);

    $byNomor = $this->actingAs($this->admin)->get(route('tiket.index', ['q' => 'ANA-2608-00001']));
    expect($byNomor->viewData('data'))->toHaveCount(1);

    $byNik = $this->actingAs($this->admin)->get(route('tiket.index', ['q' => '7371016600220002']));
    expect($byNik->viewData('data'))->toHaveCount(1)
        ->and($byNik->viewData('data')->first()->pemohon->name)->toBe('Budi Lainnya');
});

test('should separate persuratan and pengaduan queues by tab', function () {
    $pemohon = antrianPemohon('7371016600110001');
    antrianTiketSurat($pemohon, 1);
    antrianTiketAduan($pemohon, 2);

    $persuratan = $this->actingAs($this->admin)->get(route('tiket.index', ['tab' => 'persuratan']));
    expect($persuratan->viewData('data'))->toHaveCount(1)
        ->and($persuratan->viewData('data')->first()->detail_type)->toBe('pengajuan_surat');

    $pengaduan = $this->actingAs($this->admin)->get(route('tiket.index', ['tab' => 'pengaduan']));
    expect($pengaduan->viewData('data'))->toHaveCount(1)
        ->and($pengaduan->viewData('data')->first()->detail_type)->toBe('pengaduan');
});

test('should filter persuratan queue by jenis surat', function () {
    $pemohon = antrianPemohon('7371016600110001');

    $jenisLain = JenisSurat::firstOrCreate(
        ['kode' => 'SKU-Q'],
        ['nama' => 'Surat Antrian Lain', 'fields' => [], 'is_active' => true]
    );

    antrianTiketSurat($pemohon, 1);
    antrianTiketSurat($pemohon, 2, $jenisLain->id);

    $response = $this->actingAs($this->admin)
        ->get(route('tiket.index', ['tab' => 'persuratan', 'jenis_surat_id' => $jenisLain->id]));

    expect($response->viewData('data'))->toHaveCount(1)
        ->and($response->viewData('data')->first()->detail->jenis_surat_id)->toBe($jenisLain->id);
});

test('should require catatan when rejecting a ticket', function () {
    $pemohon = antrianPemohon('7371016600110001');
    antrianTiketAduan($pemohon, 1);
    $tiket = Tiket::where('nomor_tiket', 'ANA-2608-00001')->first();

    $this->actingAs($this->admin)
        ->from(route('tiket.show', $tiket->id))
        ->post(route('tiket.update-status', $tiket->id), ['status_to' => 'ditolak', 'catatan' => ''])
        ->assertRedirect(route('tiket.show', $tiket->id))
        ->assertSessionHasErrors('catatan');

    expect($tiket->fresh()->status->value)->toBe('baru');
});

test('should reject ticket with catatan and log it', function () {
    $pemohon = antrianPemohon('7371016600110001');
    antrianTiketAduan($pemohon, 1);
    $tiket = Tiket::where('nomor_tiket', 'ANA-2608-00001')->first();

    $this->actingAs($this->admin)
        ->post(route('tiket.update-status', $tiket->id), [
            'status_to' => 'ditolak',
            'catatan' => 'Data tidak lengkap, silakan ajukan ulang.',
        ])
        ->assertRedirect(route('tiket.show', $tiket->id));

    expect($tiket->fresh()->status->value)->toBe('ditolak');

    $this->assertDatabaseHas('status_log', [
        'tiket_id' => $tiket->id,
        'status_to' => 'ditolak',
        'catatan' => 'Data tidak lengkap, silakan ajukan ulang.',
    ]);
});
