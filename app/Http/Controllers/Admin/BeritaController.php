<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BeritaRequest;
use App\Models\Media;
use App\Services\BeritaService;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function __construct(
        protected BeritaService $service,
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();

        return view('pages.berita.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.berita.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BeritaRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['judul']);
        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (isset($data['konten'])) {
            $data['isi'] = $data['konten'];
        }

        $imageFile = $request->file('gambar') ?? $request->file('thumbnail');
        if ($imageFile) {
            $media = $this->fileUploadService->upload($imageFile, 'berita', 'public');
            $data['thumbnail'] = $media->path;
            $data['gambar'] = $media->path;
        } else {
            unset($data['gambar'], $data['thumbnail']);
        }

        $this->service->create($data);

        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.berita.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.berita.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BeritaRequest $request, int $id)
    {
        $berita = $this->service->find($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['judul']);
        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (isset($data['konten'])) {
            $data['isi'] = $data['konten'];
        }

        $imageFile = $request->file('gambar') ?? $request->file('thumbnail');

        if ($imageFile) {
            $currentPath = $berita->thumbnail ?? $berita->gambar;
            if ($currentPath) {
                $media = Media::where('path', $currentPath)->first();
                if ($media) {
                    $this->fileUploadService->delete($media);
                } elseif (Storage::disk('public')->exists($currentPath)) {
                    Storage::disk('public')->delete($currentPath);
                }
            }
            $media = $this->fileUploadService->upload($imageFile, 'berita', 'public');
            $data['thumbnail'] = $media->path;
            $data['gambar'] = $media->path;
        } else {
            unset($data['gambar'], $data['thumbnail']);
        }

        $this->service->update($id, $data);

        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $berita = $this->service->find($id);
        $currentPath = $berita->thumbnail ?? $berita->gambar;
        if ($currentPath) {
            $media = Media::where('path', $currentPath)->first();
            if ($media) {
                $this->fileUploadService->delete($media);
            } elseif (Storage::disk('public')->exists($currentPath)) {
                Storage::disk('public')->delete($currentPath);
            }
        }

        $this->service->delete($id);

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Berita berhasil dihapus!');
        }

        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil dihapus!');
    }
}
