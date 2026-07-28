<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\PemohonRequest;
use App\Models\Kelurahan;
use App\Services\PemohonService;
use Illuminate\Database\QueryException;

class PemohonController extends Controller
{
    public function __construct(
        protected PemohonService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all()->load('kelurahan');

        return view('pages.pemohon.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('pages.pemohon.create', compact('kelurahanList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PemohonRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        return redirect()->route('pemohon.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id)->load('kelurahan', 'tikets');

        return view('pages.pemohon.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('pages.pemohon.edit', compact('data', 'kelurahanList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PemohonRequest $request, int $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data);

        return redirect()->route('pemohon.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
        } catch (QueryException $e) {
            $message = 'Pemohon tidak dapat dihapus karena masih memiliki riwayat tiket.';

            if (request()->wantsJson()) {
                return ResponseHelper::error($message, 409);
            }

            return redirect()->route('pemohon.index')->with('error', $message);
        }

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Data berhasil dihapus!');
        }

        return redirect()->route('pemohon.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
