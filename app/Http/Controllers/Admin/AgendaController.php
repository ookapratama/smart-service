<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgendaRequest;
use App\Services\AgendaService;
use App\Services\FileUploadService;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    public function __construct(
        protected AgendaService $service,
        protected FileUploadService $fileUploadService
    ) {}

    public function index()
    {
        $data = $this->service->all();

        return view('pages.agenda.index', compact('data'));
    }

    public function create()
    {
        return view('pages.agenda.create');
    }

    public function store(AgendaRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['judul']);
        $data['is_published'] = $request->boolean('is_published');
        $data['status_agenda'] = $request->input('status_agenda', 'mendatang');

        if ($request->hasFile('gambar')) {
            $media = $this->fileUploadService->upload($request->file('gambar'), 'agenda', 'public');
            $data['gambar'] = $media->path;
        }

        if ($request->hasFile('file_lampiran')) {
            $mediaDoc = $this->fileUploadService->upload($request->file('file_lampiran'), 'agenda/docs', 'public');
            $data['file_lampiran'] = $mediaDoc->path;
        }

        $this->service->create($data);

        return redirect()->route('agenda.index')
            ->with('success', 'Agenda kegiatan berhasil ditambahkan!');
    }

    public function show(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.agenda.show', compact('data'));
    }

    public function edit(int $id)
    {
        $data = $this->service->find($id);

        return view('pages.agenda.edit', compact('data'));
    }

    public function update(AgendaRequest $request, int $id)
    {
        $agenda = $this->service->find($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['judul']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('gambar')) {
            $media = $this->fileUploadService->replace($agenda->gambar, $request->file('gambar'), 'agenda', 'public');
            $data['gambar'] = $media->path;
        } else {
            unset($data['gambar']);
        }

        if ($request->hasFile('file_lampiran')) {
            $mediaDoc = $this->fileUploadService->replace($agenda->file_lampiran, $request->file('file_lampiran'), 'agenda/docs', 'public');
            $data['file_lampiran'] = $mediaDoc->path;
        } else {
            unset($data['file_lampiran']);
        }

        $this->service->update($id, $data);

        return redirect()->route('agenda.index')
            ->with('success', 'Agenda kegiatan berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $agenda = $this->service->find($id);

        if ($agenda->gambar) {
            $this->fileUploadService->delete($agenda->gambar, 'public');
        }

        if ($agenda->file_lampiran) {
            $this->fileUploadService->delete($agenda->file_lampiran, 'public');
        }

        $this->service->delete($id);

        return redirect()->route('agenda.index')
            ->with('success', 'Agenda kegiatan berhasil dihapus!');
    }
}
