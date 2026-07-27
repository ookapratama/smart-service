<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\JadwalPelayananRequest;
use App\Services\JadwalPelayananService;

class JadwalPelayananController extends Controller
{
    public function __construct(
        protected JadwalPelayananService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();

        return view('pages.jadwal-pelayanan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.jadwal-pelayanan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JadwalPelayananRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $this->service->create($data);

        return redirect()->route('jadwal-pelayanan.index')
            ->with('success', 'Jadwal pelayanan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.jadwal-pelayanan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.jadwal-pelayanan.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JadwalPelayananRequest $request, int $id)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $this->service->update($id, $data);

        return redirect()->route('jadwal-pelayanan.index')
            ->with('success', 'Jadwal pelayanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Jadwal pelayanan berhasil dihapus!');
        }

        return redirect()->route('jadwal-pelayanan.index')
            ->with('success', 'Jadwal pelayanan berhasil dihapus!');
    }
}
