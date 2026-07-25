<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\KategoriPengaduanRequest;
use App\Services\KategoriPengaduanService;

class KategoriPengaduanController extends Controller
{
    public function __construct(
        protected KategoriPengaduanService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();

        return view('pages.kategori-pengaduan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.kategori-pengaduan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KategoriPengaduanRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        return redirect()->route('kategori-pengaduan.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.kategori-pengaduan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.kategori-pengaduan.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KategoriPengaduanRequest $request, int $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data);

        return redirect()->route('kategori-pengaduan.index')
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

        return redirect()->route('kategori-pengaduan.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
