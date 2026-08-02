@extends('layouts/layoutMaster')

@section('title', 'Edit Agenda')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Agenda /</span> Edit Agenda
        </h4>
        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Edit Agenda Kegiatan</h5>
        </div>
        <div class="card-body pt-4">
            <form action="{{ route('agenda.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="judul">Judul Agenda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $data->judul) }}" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="kategori">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="Rapat & Musyawarah" {{ old('kategori', $data->kategori) == 'Rapat & Musyawarah' ? 'selected' : '' }}>Rapat & Musyawarah</option>
                            <option value="Pelayanan Keliling" {{ old('kategori', $data->kategori) == 'Pelayanan Keliling' ? 'selected' : '' }}>Pelayanan Keliling</option>
                            <option value="Sosialisasi & Edukasi" {{ old('kategori', $data->kategori) == 'Sosialisasi & Edukasi' ? 'selected' : '' }}>Sosialisasi & Edukasi</option>
                            <option value="Kegiatan Kemasyarakatan" {{ old('kategori', $data->kategori) == 'Kegiatan Kemasyarakatan' ? 'selected' : '' }}>Kegiatan Kemasyarakatan</option>
                            <option value="Upacara & Peringatan" {{ old('kategori', $data->kategori) == 'Upacara & Peringatan' ? 'selected' : '' }}>Upacara & Peringatan</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="penyelenggara">Penyelenggara</label>
                        <input type="text" class="form-control @error('penyelenggara') is-invalid @enderror" id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara', $data->penyelenggara) }}">
                        @error('penyelenggara')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="lokasi">Lokasi Kegiatan</label>
                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi', $data->lokasi) }}">
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="mulai_at">Waktu Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('mulai_at') is-invalid @enderror" id="mulai_at" name="mulai_at" value="{{ old('mulai_at', optional($data->mulai_at)->format('Y-m-d\TH:i')) }}" required>
                        @error('mulai_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="selesai_at">Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" class="form-control @error('selesai_at') is-invalid @enderror" id="selesai_at" name="selesai_at" value="{{ old('selesai_at', optional($data->selesai_at)->format('Y-m-d\TH:i')) }}">
                        @error('selesai_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="waktu_keterangan">Keterangan Jam (Teks)</label>
                        <input type="text" class="form-control @error('waktu_keterangan') is-invalid @enderror" id="waktu_keterangan" name="waktu_keterangan" value="{{ old('waktu_keterangan', $data->waktu_keterangan) }}">
                        @error('waktu_keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="ringkasan">Ringkasan Singkat</label>
                        <textarea class="form-control @error('ringkasan') is-invalid @enderror" id="ringkasan" name="ringkasan" rows="2">{{ old('ringkasan', $data->ringkasan) }}</textarea>
                        @error('ringkasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="deskripsi">Deskripsi Detail</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi', $data->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="gambar">File Gambar Poster / Banner</label>
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
                        <small class="text-muted">Biarkan kosong jika tidak mengubah gambar.</small>
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="file_lampiran">File Lampiran PDF / Dokumen</label>
                        @if($data->file_lampiran)
                            <div class="mb-2">
                                <span class="badge bg-label-info"><i class="ri-file-line me-1"></i> Lampiran ada</span>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('file_lampiran') is-invalid @enderror" id="file_lampiran" name="file_lampiran" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip">
                        <small class="text-muted">Biarkan kosong jika tidak mengubah berkas lampiran.</small>
                        @error('file_lampiran')
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
                            <i class="ri-save-line me-1"></i> Perbarui Agenda
                        </button>
                        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
