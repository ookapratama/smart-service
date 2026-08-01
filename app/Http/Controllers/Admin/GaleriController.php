<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GaleriRequest;
use App\Services\FileUploadService;
use App\Services\GaleriService;
use Illuminate\Support\Str;

class GaleriController extends Controller
{
    public function __construct(
        protected GaleriService $service,
        protected FileUploadService $fileUploadService
    ) {}

    public function index()
    {
        $data = $this->service->all();

        return view('pages.galeri.index', compact('data'));
    }

    public function create()
    {
        return view('pages.galeri.create');
    }

    public function store(GaleriRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['judul']);
        $data['is_published'] = $request->boolean('is_published');
        $data['tipe'] = $request->input('tipe', 'foto');

        if ($request->hasFile('gambar')) {
            $media = $this->fileUploadService->upload($request->file('gambar'), 'galeri', 'public');
            $data['gambar'] = $media->path;
        }

        $this->service->create($data);

        return redirect()->route('galeri.index')
            ->with('success', 'Galeri berhasil ditambahkan!');
    }

    public function show(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.galeri.show', compact('data'));
    }

    public function edit(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.galeri.edit', compact('data'));
    }

    public function update(GaleriRequest $request, int $id)
    {
        $galeri = $this->service->find($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['judul']);
        $data['is_published'] = $request->boolean('is_published');
        $data['tipe'] = $request->input('tipe', 'foto');

        if ($request->hasFile('gambar')) {
            $media = $this->fileUploadService->replace($galeri->gambar, $request->file('gambar'), 'galeri', 'public');
            $data['gambar'] = $media->path;
        } else {
            unset($data['gambar']);
        }

        $this->service->update($id, $data);

        return redirect()->route('galeri.index')
            ->with('success', 'Galeri berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $galeri = $this->service->find($id);

        if ($galeri->gambar) {
            $this->fileUploadService->delete($galeri->gambar, 'public');
        }

        $this->service->delete($id);

        return redirect()->route('galeri.index')
            ->with('success', 'Galeri berhasil dihapus!');
    }
}
