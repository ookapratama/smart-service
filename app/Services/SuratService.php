<?php

namespace App\Services;

use App\Models\JenisSurat;
use App\Models\Media;
use App\Models\PengajuanSurat;
use App\Models\Setting;
use App\Models\SuratCounter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Penomoran surat resmi + rendering PDF surat (S3_MVP_DESIGN.md §5.3, §6, §7).
 * Nomor surat HANYA terbit saat pengajuan disetujui (tiket selesai) — pengajuan
 * ditolak tidak membakar nomor arsip. PDF ditulis ketat setelah commit.
 */
class SuratService
{
    /**
     * Format nomor surat resmi: {seq}/{kode jenis}/KEC-SRG/{tahun}.
     * Contoh: 001/SKD/KEC-SRG/2026. Satu-satunya tempat format ini
     * didefinisikan — ubah di sini bila tata naskah kecamatan berubah.
     */
    public const NOMOR_FORMAT = '%03d/%s/KEC-SRG/%s';

    public const PDF_COLLECTION = 'surat_pdf';

    /** Disk privat — PDF surat tidak boleh terakses tanpa gerbang (NIK/permission). */
    public const PDF_DISK = 'local';

    public const PDF_FOLDER = 'surat';

    public const TEMPLATE_PREFIX = 'surat.templates.';

    public function __construct(protected FileUploadService $fileUploadService) {}

    /**
     * Generate nomor surat berikutnya secara race-safe, periode per TAHUN (§6).
     * HARUS dipanggil di dalam DB::transaction() — pola sama persis dengan
     * TiketService::generateNomorTiket(): upsert atomik + lockForUpdate baca balik.
     */
    public function generateNomorSurat(string $kodeJenis): string
    {
        $periode = now()->format('Y');

        SuratCounter::upsert(
            [['periode' => $periode, 'last_seq' => 1]],
            ['periode'],
            ['last_seq' => DB::raw('last_seq + 1')]
        );

        $seq = SuratCounter::where('periode', $periode)->lockForUpdate()->value('last_seq');

        return sprintf(self::NOMOR_FORMAT, $seq, $kodeJenis, $periode);
    }

    /**
     * Terbitkan nomor surat untuk pengajuan yang disetujui — idempotent:
     * nomor yang sudah terbit tidak pernah diganti/dibakar ulang.
     * HARUS dipanggil di dalam transaksi status-selesai (§7 Atomicity).
     */
    public function assignNomorSurat(PengajuanSurat $pengajuan): PengajuanSurat
    {
        if ($pengajuan->nomor_surat !== null) {
            return $pengajuan;
        }

        $pengajuan->nomor_surat = $this->generateNomorSurat($pengajuan->jenisSurat->kode);
        $pengajuan->save();

        return $pengajuan;
    }

    /**
     * Render PDF surat resmi dan simpan sebagai Media (collection surat_pdf).
     * Dipanggil SETELAH commit transaksi status (§7 Durability) — kegagalan di
     * sini tidak boleh membatalkan perubahan status; caller menangkap exception.
     * Regenerate-safe: PDF lama dihapus sebelum yang baru disimpan.
     */
    public function generatePdf(PengajuanSurat $pengajuan): Media
    {
        $pengajuan->loadMissing(['jenisSurat', 'tiket.pemohon.kelurahan']);

        $pdf = Pdf::loadView($this->resolveTemplateView($pengajuan->jenisSurat), [
            'pengajuan' => $pengajuan,
            'jenisSurat' => $pengajuan->jenisSurat,
            'tiket' => $pengajuan->tiket,
            'pemohon' => $pengajuan->tiket?->pemohon,
            'nomorSurat' => $pengajuan->nomor_surat,
            'tanggal' => now()->locale('id')->translatedFormat('d F Y'),
            'profil' => $this->profilKecamatan(),
            'penandatangan' => $this->penandatangan(),
        ])->setPaper('a4');

        $contents = $pdf->output();

        // Regenerate: buang PDF lama (file + row media) agar tidak ada orphan.
        $pengajuan->media()
            ->where('collection', self::PDF_COLLECTION)
            ->get()
            ->each(fn (Media $old) => $this->fileUploadService->delete($old));

        $namaFile = Str::slug($pengajuan->nomor_surat ?: 'pengajuan-'.$pengajuan->id).'.pdf';

        $media = $this->fileUploadService->uploadContents($contents, $namaFile, self::PDF_FOLDER, self::PDF_DISK, [
            'collection' => self::PDF_COLLECTION,
            'mime_type' => 'application/pdf',
            'original_name' => 'Surat-'.($pengajuan->jenisSurat->kode ?? 'S3').'-'.($pengajuan->tiket?->nomor_tiket ?? $pengajuan->id).'.pdf',
        ]);

        $pengajuan->media()->save($media);

        return $media;
    }

    /**
     * PDF surat resmi milik pengajuan (null jika belum/gagal dibuat).
     */
    public function pdfMedia(PengajuanSurat $pengajuan): ?Media
    {
        return $pengajuan->media()
            ->where('collection', self::PDF_COLLECTION)
            ->latest('id')
            ->first();
    }

    /**
     * Template per jenis: template_view eksplisit → {kode}.blade.php → generik.
     * template_view menerima nama pendek dari form admin ('keterangan',
     * 'pengantar', 'skd') maupun nama view lengkap; keduanya diguard
     * View::exists sehingga nilai salah jatuh aman ke generik.
     */
    public function resolveTemplateView(?JenisSurat $jenisSurat): string
    {
        $templateView = $jenisSurat?->template_view;

        if ($templateView && ! str_contains($templateView, '.')) {
            $templateView = self::TEMPLATE_PREFIX.$templateView;
        }

        $candidates = array_filter([
            $templateView,
            $jenisSurat ? self::TEMPLATE_PREFIX.Str::lower($jenisSurat->kode) : null,
        ]);

        foreach ($candidates as $view) {
            if (View::exists($view)) {
                return $view;
            }
        }

        return self::TEMPLATE_PREFIX.'generik';
    }

    /**
     * Penandatangan surat dari settings (group penandatangan).
     *
     * @return array<string, string|null>
     */
    protected function penandatangan(): array
    {
        return [
            'jabatan' => Setting::getByKey('ttd_jabatan') ?: 'Camat Soreang',
            'nama' => Setting::getByKey('ttd_nama'),
            'nip' => Setting::getByKey('ttd_nip'),
        ];
    }

    /**
     * Profil kecamatan dari settings (key profile_* — SettingSeeder).
     *
     * @return array<string, string|null>
     */
    protected function profilKecamatan(): array
    {
        return [
            'kecamatan' => Setting::getByKey('profile_kecamatan', 'Kecamatan Soreang'),
            'kota' => Setting::getByKey('profile_kota', 'Kota Parepare'),
            'alamat' => Setting::getByKey('profile_alamat', ''),
            'telepon' => Setting::getByKey('profile_telepon', ''),
            'email' => Setting::getByKey('profile_email', ''),
        ];
    }
}
