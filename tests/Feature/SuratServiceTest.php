<?php

use App\Enums\TiketStatus;
use App\Models\JenisSurat;
use App\Models\KategoriPengaduan;
use App\Models\Media;
use App\Models\Pemohon;
use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Models\SuratCounter;
use App\Models\Tiket;
use App\Repositories\TiketRepository;
use App\Services\FileUploadService;
use App\Services\SuratService;
use App\Services\TiketService;
use Illuminate\Support\Facades\Storage;

function buatTiketPengajuan(string $status = 'diproses', string $kode = 'SKD-U'): Tiket
{
    $pemohon = Pemohon::firstOrCreate(
        ['nik' => '7372019999888877'],
        ['name' => 'Warga Surat Uji', 'phone' => '081200003333', 'alamat' => 'Jl. Uji Surat No. 9']
    );

    $jenis = JenisSurat::firstOrCreate(
        ['kode' => $kode],
        [
            'nama' => 'Surat Uji '.$kode,
            'fields' => [
                ['name' => 'alamat_domisili', 'type' => 'textarea', 'label' => 'Alamat Domisili', 'required' => true],
            ],
            'is_active' => true,
        ]
    );

    $pengajuan = PengajuanSurat::create([
        'jenis_surat_id' => $jenis->id,
        'keperluan' => 'Keperluan pengujian surat',
        'data' => ['alamat_domisili' => 'Jl. Domisili Uji No. 1'],
    ]);

    return $pengajuan->tiket()->create([
        'nomor_tiket' => 'UJI-2607-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Pengajuan Uji',
        'status' => $status,
        'channel' => 'web',
    ]);
}

function buatTiketPengaduan(string $status = 'diproses'): Tiket
{
    $pemohon = Pemohon::firstOrCreate(
        ['nik' => '7372018888777766'],
        ['name' => 'Warga Aduan Uji', 'phone' => '081200004444']
    );

    $kategori = KategoriPengaduan::firstOrCreate(['nama' => 'Kategori Surat Uji']);

    $pengaduan = Pengaduan::create([
        'kategori_pengaduan_id' => $kategori->id,
        'deskripsi' => 'Deskripsi aduan uji',
        'lokasi' => 'Jl. Aduan No. 2',
    ]);

    return $pengaduan->tiket()->create([
        'nomor_tiket' => 'ADU-2607-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Pengaduan Uji',
        'status' => $status,
        'channel' => 'web',
    ]);
}

beforeEach(function () {
    Storage::fake(SuratService::PDF_DISK);

    $this->surat = app(SuratService::class);
    $this->tiketService = app(TiketService::class);
});

test('nomor surat is sequential within the yearly periode', function () {
    $tahun = now()->format('Y');

    expect($this->surat->generateNomorSurat('SKD'))->toBe("001/SKD/KEC-SRG/{$tahun}");
    expect($this->surat->generateNomorSurat('SKD'))->toBe("002/SKD/KEC-SRG/{$tahun}");

    expect(SuratCounter::where('periode', $tahun)->value('last_seq'))->toBe(2);
});

test('pengajuan transitioning to selesai gets nomor surat exactly once and a pdf media', function () {
    $tiket = buatTiketPengajuan('diproses');

    $this->tiketService->updateStatus($tiket->id, 'selesai');

    $pengajuan = $tiket->detail->fresh();
    $tahun = now()->format('Y');
    expect($pengajuan->nomor_surat)->toBe("001/SKD-U/KEC-SRG/{$tahun}");
    expect($this->tiketService->pdfWarning)->toBeNull();

    // PDF tercipta + row media collection surat_pdf menempel ke pengajuan.
    $pdf = $pengajuan->media()->where('collection', SuratService::PDF_COLLECTION)->first();
    expect($pdf)->not->toBeNull();
    expect($pdf->mime_type)->toBe('application/pdf');
    Storage::disk(SuratService::PDF_DISK)->assertExists($pdf->path);

    // Idempotent: pemanggilan ulang tidak membakar/mengganti nomor.
    $this->surat->assignNomorSurat($pengajuan);
    expect($pengajuan->fresh()->nomor_surat)->toBe("001/SKD-U/KEC-SRG/{$tahun}");
    expect(SuratCounter::where('periode', $tahun)->value('last_seq'))->toBe(1);

    // Selesai adalah status terminal: transisi ulang ditolak, nomor tetap.
    expect(fn () => $this->tiketService->updateStatus($tiket->id, 'selesai'))
        ->toThrow(InvalidArgumentException::class);
    expect($pengajuan->fresh()->nomor_surat)->toBe("001/SKD-U/KEC-SRG/{$tahun}");
});

test('two approvals produce sequential nomor surat', function () {
    $pertama = buatTiketPengajuan('diproses');
    $kedua = buatTiketPengajuan('diproses', 'SKU-U');

    $this->tiketService->updateStatus($pertama->id, 'selesai');
    $this->tiketService->updateStatus($kedua->id, 'selesai');

    $tahun = now()->format('Y');
    expect($pertama->detail->fresh()->nomor_surat)->toBe("001/SKD-U/KEC-SRG/{$tahun}");
    expect($kedua->detail->fresh()->nomor_surat)->toBe("002/SKU-U/KEC-SRG/{$tahun}");
});

test('rejected pengajuan does not burn a nomor surat', function () {
    $tiket = buatTiketPengajuan('diproses');

    $this->tiketService->updateStatus($tiket->id, 'ditolak', 'Berkas tidak lengkap');

    expect($tiket->detail->fresh()->nomor_surat)->toBeNull();
    expect(SuratCounter::count())->toBe(0);
    expect(Media::where('collection', SuratService::PDF_COLLECTION)->count())->toBe(0);
});

test('pengaduan transitioning to selesai gets no nomor surat and no pdf', function () {
    $tiket = buatTiketPengaduan('diproses');

    $this->tiketService->updateStatus($tiket->id, 'selesai');

    expect($tiket->fresh()->status)->toBe(TiketStatus::Selesai);
    expect(SuratCounter::count())->toBe(0);
    expect(Media::where('collection', SuratService::PDF_COLLECTION)->count())->toBe(0);
});

test('regenerating the pdf replaces the old file instead of stacking media rows', function () {
    $tiket = buatTiketPengajuan('diproses');
    $this->tiketService->updateStatus($tiket->id, 'selesai');

    $pengajuan = $tiket->detail->fresh();
    $lama = $pengajuan->media()->where('collection', SuratService::PDF_COLLECTION)->first();

    $this->surat->generatePdf($pengajuan);

    $semuaPdf = $pengajuan->media()->where('collection', SuratService::PDF_COLLECTION)->get();
    expect($semuaPdf)->toHaveCount(1);
    expect(Media::find($lama->id))->toBeNull();
    Storage::disk(SuratService::PDF_DISK)->assertExists($semuaPdf->first()->path);
});

test('public unduh route requires matching nik and a finished ticket', function () {
    $tiket = buatTiketPengajuan('diproses');
    $this->tiketService->updateStatus($tiket->id, 'selesai');

    $nik = $tiket->pemohon->nik;

    // NIK salah → error validasi, file tidak terkirim.
    $this->get(route('surat.unduh', $tiket->nomor_tiket).'?nik=1111222233334444')
        ->assertSessionHasErrors('nik');

    // NIK cocok → PDF terunduh.
    $this->get(route('surat.unduh', $tiket->nomor_tiket).'?nik='.$nik)
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    // Tiket belum selesai → 404 meski NIK benar.
    $belumSelesai = buatTiketPengajuan('baru', 'SKP-U');
    $this->get(route('surat.unduh', $belumSelesai->nomor_tiket).'?nik='.$nik)
        ->assertNotFound();
});

test('pdf failure does not roll back the status update or the nomor surat', function () {
    $tiket = buatTiketPengajuan('diproses');

    // Simulasikan kegagalan render: SuratService palsu yang meledak di generatePdf.
    $meledak = new class(app(FileUploadService::class)) extends SuratService
    {
        public function generatePdf(PengajuanSurat $pengajuan): Media
        {
            throw new RuntimeException('dompdf meledak');
        }
    };

    $service = new TiketService(app(TiketRepository::class), $meledak);
    $service->updateStatus($tiket->id, 'selesai');

    expect($tiket->fresh()->status)->toBe(TiketStatus::Selesai);
    expect($tiket->detail->fresh()->nomor_surat)->not->toBeNull();
    expect($service->pdfWarning)->not->toBeNull();
});
