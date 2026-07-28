<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\JenisSuratRequest;
use App\Services\JenisSuratService;

class JenisSuratController extends Controller
{
    public function __construct(
        protected JenisSuratService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();

        return view('pages.jenis-surat.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.jenis-surat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JenisSuratRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        return redirect()->route('jenis-surat.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.jenis-surat.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.jenis-surat.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JenisSuratRequest $request, int $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data);

        return redirect()->route('jenis-surat.index')
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

        return redirect()->route('jenis-surat.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
