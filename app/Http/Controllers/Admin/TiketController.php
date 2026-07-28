<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TiketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TiketRequest;
use App\Services\FileUploadService;
use App\Services\SuratService;
use App\Services\TiketService;
use Illuminate\Http\Request;

/**
 * Monitoring & manajemen status tiket. Tidak ada create/edit/delete generik —
 * tiket dihasilkan oleh alur pengajuan modul (Persuratan/Pengaduan) yang
 * mem-produce detail (morph) sekaligus nomor_tiket-nya, bukan dari sini.
 */
class TiketController extends Controller
{
    public function __construct(
        protected TiketService $service,
        protected SuratService $suratService,
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Antrian tiket dengan filter status/channel.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'channel']);
        $data = $this->service->filtered($filters);

        return view('pages.tiket.index', compact('data', 'filters'));
    }

    /**
     * Detail tiket + riwayat status.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id)->load(['pemohon', 'detail', 'statusLogs.user', 'assignedTo']);

        return view('pages.tiket.show', compact('data'));
    }

    /**
     * Transisikan status tiket (mis. baru -> diproses -> selesai/ditolak).
     */
    public function updateStatus(TiketRequest $request, int $id)
    {
        try {
            $this->service->updateStatus($id, $request->validated('status_to'), $request->validated('catatan'));

            $redirect = redirect()->route('tiket.show', $id)->with('success', 'Status tiket berhasil diperbarui!');

            if ($this->service->pdfWarning) {
                $redirect->with('warning', $this->service->pdfWarning);
            }

            return $redirect;
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Unduh lampiran syarat milik detail tiket (disk bisa privat/publik).
     */
    public function downloadLampiran(int $id, int $mediaId)
    {
        $tiket = $this->service->find($id);

        abort_unless($tiket->detail, 404);

        $media = $tiket->detail->media()
            ->where('collection', '!=', SuratService::PDF_COLLECTION)
            ->findOrFail($mediaId);

        return $this->fileUploadService->download($media);
    }

    /**
     * Unduh PDF surat resmi yang sudah di-generate.
     */
    public function downloadSuratPdf(int $id)
    {
        $tiket = $this->service->find($id);

        abort_unless($tiket->detail_type === 'pengajuan_surat', 404);

        $media = $this->suratService->pdfMedia($tiket->detail);

        abort_unless($media, 404);

        return $this->fileUploadService->download($media);
    }

    /**
     * Generate ulang PDF surat (mis. setelah kegagalan render pasca-commit).
     * Hanya untuk pengajuan yang sudah selesai + bernomor surat (§5.3).
     */
    public function regenerateSuratPdf(int $id)
    {
        $tiket = $this->service->find($id);

        abort_unless(
            $tiket->detail_type === 'pengajuan_surat'
            && $tiket->status === TiketStatus::Selesai
            && $tiket->detail?->nomor_surat,
            404
        );

        try {
            $this->suratService->generatePdf($tiket->detail);

            return redirect()->route('tiket.show', $id)->with('success', 'PDF surat berhasil dibuat ulang!');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('tiket.show', $id)->with('error', 'PDF surat gagal dibuat. Silakan coba lagi.');
        }
    }
}
