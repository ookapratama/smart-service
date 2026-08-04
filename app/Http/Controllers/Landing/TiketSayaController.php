<?php

namespace App\Http\Controllers\Landing;

use App\Enums\TiketStatus;
use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Services\FileUploadService;
use App\Services\SuratService;
use Illuminate\Http\Request;

/**
 * "Tiket Saya" (S3_MVP_DESIGN.md §5.2): riwayat semua tiket milik warga yang
 * login — pengaduan + pengajuan surat — beserta unduhan PDF surat jadi.
 * Kepemilikan selalu dipagari pemohon_id; tiket milik orang lain = 404.
 */
class TiketSayaController extends Controller
{
    public function __construct(
        protected SuratService $suratService,
        protected FileUploadService $fileUploadService
    ) {}

    public function index(Request $request)
    {
        $pemohon = $request->user()->pemohon;

        if (! $pemohon) {
            // Akun staf tidak punya pemohon — halaman ini bukan untuk mereka.
            return redirect()->route('dashboard');
        }

        $type = $request->query('type');
        $query = Tiket::where('pemohon_id', $pemohon->id)
            ->with('detail')
            ->latest();

        if (in_array($type, ['pengajuan_surat', 'pengaduan'], true)) {
            $query->where('detail_type', $type);
        }

        $tikets = $query->paginate(10)->withQueryString();

        return view('home.tiket-saya.index', compact('tikets', 'pemohon', 'type'));
    }

    public function show(Request $request, string $nomorTiket)
    {
        $pemohon = $request->user()->pemohon;

        abort_unless($pemohon !== null, 404);

        $tiket = Tiket::with(['detail.media', 'detail.kategori', 'statusLogs.user', 'pemohon.kelurahan'])
            ->where('nomor_tiket', $nomorTiket)
            ->where('pemohon_id', $pemohon->id)
            ->firstOrFail();

        $suratSiap = $tiket->detail_type === 'pengajuan_surat'
            && $tiket->status === TiketStatus::Selesai
            && $this->suratService->pdfMedia($tiket->detail) !== null;

        return view('home.tiket-saya.show', compact('tiket', 'suratSiap'));
    }

    /**
     * Unduh PDF surat milik sendiri — sesi login menggantikan gerbang publik
     * nomor tiket + NIK (identitas sudah terbukti lewat OTP).
     */
    public function unduhSurat(Request $request, string $nomorTiket)
    {
        $pemohon = $request->user()->pemohon;

        abort_unless($pemohon !== null, 404);

        $tiket = Tiket::with('detail')
            ->where('nomor_tiket', $nomorTiket)
            ->where('pemohon_id', $pemohon->id)
            ->where('detail_type', 'pengajuan_surat')
            ->firstOrFail();

        abort_unless($tiket->status === TiketStatus::Selesai, 404);

        $media = $this->suratService->pdfMedia($tiket->detail);

        abort_unless($media, 404);

        return $this->fileUploadService->download($media);
    }
}
