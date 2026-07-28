<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TiketRequest;
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
        protected TiketService $service
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

            return redirect()->route('tiket.show', $id)->with('success', 'Status tiket berhasil diperbarui!');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
