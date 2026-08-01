@extends('layouts/layoutMaster')

@section('title', 'Edit Galeri')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Galeri /</span> Edit Galeri
        </h4>
        <a href="{{ route('galeri.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Edit Galeri Foto & Video</h5>
        </div>
        <div class="card-body pt-4">
            <form action="{{ route('galeri.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="judul">Judul Galeri <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $data->judul) }}" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="kategori">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="Kegiatan Kecamatan" {{ old('kategori', $data->kategori) == 'Kegiatan Kecamatan' ? 'selected' : '' }}>Kegiatan Kecamatan</option>
                            <option value="Pelayanan Publik" {{ old('kategori', $data->kategori) == 'Pelayanan Publik' ? 'selected' : '' }}>Pelayanan Publik</option>
                            <option value="UMKM & Ekonomi" {{ old('kategori', $data->kategori) == 'UMKM & Ekonomi' ? 'selected' : '' }}>UMKM & Ekonomi</option>
                            <option value="Sosialisasi & Edukasi" {{ old('kategori', $data->kategori) == 'Sosialisasi & Edukasi' ? 'selected' : '' }}>Sosialisasi & Edukasi</option>
                            <option value="Infrastruktur" {{ old('kategori', $data->kategori) == 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="tipe">Tipe Media</label>
                        <select class="form-select @error('tipe') is-invalid @enderror" id="tipe" name="tipe">
                            <option value="foto" {{ old('tipe', $data->tipe) == 'foto' ? 'selected' : '' }}>Foto / Gambar</option>
                            <option value="video" {{ old('tipe', $data->tipe) == 'video' ? 'selected' : '' }}>Video (YouTube Embed)</option>
                        </select>
                        @error('tipe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="tgl_kegiatan">Tanggal Kegiatan</label>
                        <input type="date" class="form-control @error('tgl_kegiatan') is-invalid @enderror" id="tgl_kegiatan" name="tgl_kegiatan" value="{{ old('tgl_kegiatan', optional($data->tgl_kegiatan)->format('Y-m-d')) }}">
                        @error('tgl_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="video_url">URL Video (Opsional)</label>
                        <input type="url" class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url" value="{{ old('video_url', $data->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                        @error('video_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="gambar">File Gambar / Cover Thumbnail</label>
                        @if($data->gambar)
                            <div class="mb-2">
                                @php
                                    $imgPath = $data->gambar;
                                    if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                                        $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                                    }
                                @endphp
                                <img src="{{ $imgPath }}" alt="preview" class="rounded border p-1" height="80">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="keterangan">Keterangan / Deskripsi</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="4">{{ old('keterangan', $data->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $data->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_published">Publikasikan ke Landing Page</label>
                        </div>
                    </div>

                    <div class="col-md-12 border-top pt-4 mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-save-line me-1"></i> Perbarui Galeri
                        </button>
                        <a href="{{ route('galeri.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
