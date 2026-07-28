<?php

namespace App\Http\Controllers\Api;

use App\Enums\TiketStatus;
use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\JadwalPelayanan;
use App\Models\JenisSurat;
use App\Models\Tiket;
use App\Services\SuratService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandingApiController extends Controller
{
    /**
     * Get list of active jenis surat.
     */
    public function jenisSurat(): JsonResponse
    {
        $surat = JenisSurat::where('is_active', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => $surat,
        ]);
    }

    /**
     * Get service schedule by kelurahan or all.
     */
    public function jadwalPelayanan(Request $request): JsonResponse
    {
        $query = JadwalPelayanan::active()->with('kelurahan');
        if ($request->has('kelurahan') && $request->kelurahan != '') {
            $keyword = $request->kelurahan;
            $query->whereHas('kelurahan', fn ($q) => $q->where('nama', 'like', "%{$keyword}%"));
        }

        $jadwal = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $jadwal,
        ]);
    }

    /**
     * Check ticket status using ticket number or NIK.
     */
    public function cekStatus(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:3',
        ]);

        $keyword = trim($request->keyword);

        $tiket = Tiket::with('pemohon')
            ->where(function ($q) use ($keyword) {
                $q->where('nomor_tiket', $keyword)
                    ->orWhereHas('pemohon', function ($p) use ($keyword) {
                        $p->where('nik', $keyword);
                    });
            })
            ->latest()
            ->first();

        if (! $tiket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data permohonan/tiket tidak ditemukan. Silakan periksa kembali Nomor Tiket atau NIK Anda.',
            ], 404);
        }

        $latestLog = $tiket->statusLogs()->latest()->first();
        $catatanRespon = $latestLog && ! empty($latestLog->catatan) ? $latestLog->catatan : null;

        // Surat jadi (persuratan selesai + PDF terbit): arahkan ke halaman
        // pengajuan, tempat unduhan digerbangi nomor tiket + NIK (§3/§5.3).
        $suratUrl = null;
        if (
            $tiket->detail_type === 'pengajuan_surat'
            && $tiket->status === TiketStatus::Selesai
            && $tiket->detail?->media()->where('collection', SuratService::PDF_COLLECTION)->exists()
        ) {
            $suratUrl = route('surat.sukses', $tiket->nomor_tiket);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'nomor_tiket' => $tiket->nomor_tiket,
                'judul' => $tiket->judul,
                'status' => is_object($tiket->status) ? $tiket->status->value ?? $tiket->status : $tiket->status,
                'status_label' => is_object($tiket->status) && method_exists($tiket->status, 'label') ? $tiket->status->label() : strtoupper((string) $tiket->status),
                'channel' => is_object($tiket->channel) ? $tiket->channel->value ?? $tiket->channel : $tiket->channel,
                'pemohon_nama' => $tiket->pemohon ? ($tiket->pemohon->name ?? $tiket->pemohon->nama) : 'N/A',
                'instansi_nama' => 'Kecamatan Soreang',
                'created_at' => $tiket->created_at ? $tiket->created_at->format('d M Y H:i') : '-',
                'updated_at' => $tiket->updated_at ? $tiket->updated_at->format('d M Y H:i') : '-',
                'catatan_respon' => $catatanRespon,
                'surat_url' => $suratUrl,
            ],
        ]);
    }

    /**
     * Get latest published news.
     */
    public function berita(): JsonResponse
    {
        $berita = Berita::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $berita,
        ]);
    }

    /**
     * Get upcoming events (MVP).
     */
    public function events(): JsonResponse
    {
        $events = [
            [
                'id' => 1,
                'judul' => 'Sosialisasi Digitalisasi Layanan Publik 3S',
                'tanggal' => '15 Agustus 2026',
                'waktu' => '09:00 WIB',
                'lokasi' => 'Aula Kecamatan Soreang',
                'penyelenggara' => 'Kecamatan Soreang',
            ],
            [
                'id' => 2,
                'judul' => 'Brimob & Pelayanan Keliling Administrasi Warga',
                'tanggal' => '22 Agustus 2026',
                'waktu' => '08:00 - 14:00 WIB',
                'lokasi' => 'Lapangan Kelurahan Lakessi',
                'penyelenggara' => 'Tim 3S & Disdukcapil',
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $events,
        ]);
    }
}
