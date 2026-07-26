<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\InstansiRequest;
use App\Jobs\SyncWilayahJob;
use App\Models\Instansi;
use App\Services\InstansiService;
use App\Services\WilayahSyncService;
use Throwable;

class InstansiController extends Controller
{
    public function __construct(
        protected InstansiService $service,
        protected WilayahSyncService $wilayahSyncService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all()->load('parent');
        $wilayahSync = $this->wilayahSyncService->currentStatus();

        return view('pages.instansi.index', compact('data', 'wilayahSync'));
    }

    /**
     * Sinkronkan data wilayah (provinsi/kab-kota/kecamatan) dari wilayah.id.
     */
    public function syncWilayah()
    {
        if ($this->wilayahSyncService->currentStatus()['status'] === 'running') {
            return redirect()->back()->with('error', 'Sinkronisasi wilayah sedang berjalan di background. Silakan tunggu hingga selesai.');
        }

        try {
            // Synchronously sync Provinces and Regencies so basic dropdown data is immediately stored in DB
            $syncedCount = $this->wilayahSyncService->syncProvincesAndRegencies();

            // Dispatch background queue job for remaining district data sync
            SyncWilayahJob::dispatch();

            return redirect()->back()->with('success', "Sinkronisasi data wilayah berhasil diproses! {$syncedCount['provinces']} Provinsi & {$syncedCount['regencies']} Kabupaten/Kota telah tersimpan langsung ke database.");
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyinkronkan data wilayah: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = Instansi::orderBy('name')->get();

        return view('pages.instansi.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InstansiRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        return redirect()->route('instansi.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id)->load(['parent', 'children']);

        return view('pages.instansi.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);
        $parents = Instansi::where('id', '!=', $id)->orderBy('name')->get();

        return view('pages.instansi.edit', compact('data', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InstansiRequest $request, int $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data);

        return redirect()->route('instansi.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Data berhasil dihapus!');
        }

        return redirect()->route('instansi.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
