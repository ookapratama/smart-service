<?php

use App\Models\JenisSurat;
use App\Models\Pemohon;
use App\Models\PengajuanSurat;
use App\Models\Setting;
use App\Models\Tiket;
use App\Services\SuratService;
use App\Services\TiketService;
use Illuminate\Support\Facades\Storage;

function templateTiket(string $kode, string $templateView, array $fields = [], array $data = []): Tiket
{
    $pemohon = Pemohon::firstOrCreate(
        ['nik' => '7372017777888899'],
        ['name' => 'Warga Template Uji', 'phone' => '081200008888', 'alamat' => 'Jl. Template No. 5']
    );

    $jenis = JenisSurat::firstOrCreate(
        ['kode' => $kode],
        ['nama' => 'Surat Uji '.$kode, 'template_view' => $templateView, 'fields' => $fields, 'is_active' => true]
    );

    $pengajuan = PengajuanSurat::create([
        'jenis_surat_id' => $jenis->id,
        'keperluan' => 'Keperluan template uji',
        'data' => $data,
    ]);

    return $pengajuan->tiket()->create([
        'nomor_tiket' => 'TPL-2608-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Template Uji',
        'status' => 'diproses',
        'channel' => 'web',
    ]);
}

beforeEach(function () {
    Storage::fake(SuratService::PDF_DISK);
    $this->surat = app(SuratService::class);
});

test('should render keterangan and pengantar templates with penandatangan values', function () {
    Setting::where('key', 'ttd_jabatan')->update(['value' => 'Camat Soreang']);
    Setting::where('key', 'ttd_nama')->update(['value' => 'Drs. H. Uji Penandatangan']);
    Setting::where('key', 'ttd_nip')->update(['value' => '197001011990031001']);

    $tiket = templateTiket('SKTM-T', 'keterangan',
        [['name' => 'pekerjaan', 'type' => 'text', 'label' => 'Pekerjaan', 'required' => true]],
        ['pekerjaan' => 'Nelayan']);

    $viewData = [
        'pengajuan' => $tiket->detail,
        'jenisSurat' => $tiket->detail->jenisSurat,
        'tiket' => $tiket,
        'pemohon' => $tiket->pemohon,
        'nomorSurat' => '001/SKTM-T/KEC-SRG/2026',
        'tanggal' => '3 Agustus 2026',
        'profil' => ['kecamatan' => 'Kecamatan Soreang', 'kota' => 'Kota Parepare', 'alamat' => '', 'telepon' => '', 'email' => ''],
        'penandatangan' => ['jabatan' => 'Camat Soreang', 'nama' => 'Drs. H. Uji Penandatangan', 'nip' => '197001011990031001'],
    ];

    $keterangan = view('surat.templates.keterangan', $viewData)->render();
    expect($keterangan)->toContain('menerangkan bahwa')
        ->toContain('Drs. H. Uji Penandatangan')
        ->toContain('197001011990031001')
        ->toContain('Pekerjaan')
        ->toContain('Nelayan');

    $pengantar = view('surat.templates.pengantar', $viewData)->render();
    expect($pengantar)->toContain('memberikan pengantar kepada')
        ->toContain('Drs. H. Uji Penandatangan')
        ->not->toContain('menerangkan bahwa');
});

test('should resolve template per category with short template_view names', function () {
    $keterangan = new JenisSurat(['kode' => 'SKTM', 'template_view' => 'keterangan']);
    $pengantar = new JenisSurat(['kode' => 'SPKTP', 'template_view' => 'pengantar']);
    $skd = new JenisSurat(['kode' => 'SKD', 'template_view' => 'skd']);

    expect($this->surat->resolveTemplateView($keterangan))->toBe('surat.templates.keterangan')
        ->and($this->surat->resolveTemplateView($pengantar))->toBe('surat.templates.pengantar')
        ->and($this->surat->resolveTemplateView($skd))->toBe('surat.templates.skd');
});

test('should fall back to generik when template_view points to nonexistent view', function () {
    $jenis = new JenisSurat(['kode' => 'XXX', 'template_view' => 'template-tidak-ada']);

    expect($this->surat->resolveTemplateView($jenis))->toBe('surat.templates.generik');
});

test('should generate pdf through keterangan template without penandatangan settings', function () {
    // Settings ttd kosong → template harus jatuh ke default tanpa exception.
    Setting::whereIn('key', ['ttd_nama', 'ttd_nip'])->update(['value' => null]);

    $tiket = templateTiket('SKTM-T', 'keterangan');

    app(TiketService::class)->updateStatus($tiket->id, 'selesai');

    expect(app(TiketService::class)->pdfWarning)->toBeNull()
        ->and($this->surat->pdfMedia($tiket->detail->fresh()))->not->toBeNull();
});

test('should set template_view only where null when data migration runs', function () {
    JenisSurat::create(['kode' => 'SKTM', 'nama' => 'SKTM Prod', 'fields' => [], 'is_active' => true]);
    JenisSurat::create(['kode' => 'SPKTP', 'nama' => 'SPKTP Prod', 'fields' => [], 'template_view' => 'generik', 'is_active' => true]);

    $migration = include database_path('migrations/2026_08_03_000400_set_template_view_on_jenis_surat.php');
    $migration->up();
    $migration->up();

    expect(JenisSurat::where('kode', 'SKTM')->value('template_view'))->toBe('keterangan')
        // Nilai yang sudah diisi (pilihan admin) tidak boleh ditimpa.
        ->and(JenisSurat::where('kode', 'SPKTP')->value('template_view'))->toBe('generik');
});
