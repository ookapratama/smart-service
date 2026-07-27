@extends('layouts/layoutMaster')

@section('title', 'Edit Berita')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('berita.index') }}">Berita</a> /</span> Edit
    </h4>

    <div class="row">
        <div class="col-md-9 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Edit Berita</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('berita.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="judul">Judul Berita</label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $data->judul) }}" placeholder="Masukkan judul berita..." required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kategori">Kategori</label>
                                <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Layanan Public" {{ old('kategori', $data->kategori) == 'Layanan Public' ? 'selected' : '' }}>Layanan Public</option>
                                    <option value="Inovasi Digital" {{ old('kategori', $data->kategori) == 'Inovasi Digital' ? 'selected' : '' }}>Inovasi Digital</option>
                                    <option value="Kegiatan Kecamatan" {{ old('kategori', $data->kategori) == 'Kegiatan Kecamatan' ? 'selected' : '' }}>Kegiatan Kecamatan</option>
                                    <option value="Pengumuman" {{ old('kategori', $data->kategori) == 'Pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="penulis">Penulis / Sumber</label>
                                <input type="text" class="form-control @error('penulis') is-invalid @enderror" id="penulis" name="penulis" value="{{ old('penulis', $data->penulis) }}">
                                @error('penulis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="gambar">Ganti Gambar Banner</label>
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*">
                                @php
                                    $imgPath = $data->thumbnail ?? $data->gambar;
                                    if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                                        $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                                    }
                                @endphp
                                @if($imgPath)
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Gambar saat ini:</small>
                                        <img src="{{ $imgPath }}" alt="{{ $data->judul }}" class="rounded border" style="height: 80px; width: 120px; object-fit: cover;">
                                    </div>
                                @endif
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="ringkasan">Ringkasan Berita</label>
                                <textarea class="form-control @error('ringkasan') is-invalid @enderror" id="ringkasan" name="ringkasan" rows="2">{{ old('ringkasan', $data->ringkasan) }}</textarea>
                                @error('ringkasan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="konten">Isi Konten Lengkap</label>
                                <textarea class="form-control @error('konten') is-invalid @enderror" id="konten" name="konten" rows="6">{{ old('konten', $data->konten) }}</textarea>
                                @error('konten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $data->is_published) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">Publikasikan Langsung ke Landing Page</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Perbarui Berita</button>
                            <a href="{{ route('berita.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
