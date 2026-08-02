@extends('layouts/layoutMaster')

@section('title', 'Tambah Agenda')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Agenda /</span> Tambah Agenda
        </h4>
        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Form Tambah Agenda Kegiatan</h5>
        </div>
        <div class="card-body pt-4">
            <form action="{{ route('agenda.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="judul">Judul Agenda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Rapat Musrenbang Pembangunan Kecamatan" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="kategori">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="Rapat & Musyawarah" {{ old('kategori') == 'Rapat & Musyawarah' ? 'selected' : '' }}>Rapat & Musyawarah</option>
                            <option value="Pelayanan Keliling" {{ old('kategori') == 'Pelayanan Keliling' ? 'selected' : '' }}>Pelayanan Keliling</option>
                            <option value="Sosialisasi & Edukasi" {{ old('kategori') == 'Sosialisasi & Edukasi' ? 'selected' : '' }}>Sosialisasi & Edukasi</option>
                            <option value="Kegiatan Kemasyarakatan" {{ old('kategori') == 'Kegiatan Kemasyarakatan' ? 'selected' : '' }}>Kegiatan Kemasyarakatan</option>
                            <option value="Upacara & Peringatan" {{ old('kategori') == 'Upacara & Peringatan' ? 'selected' : '' }}>Upacara & Peringatan</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="penyelenggara">Penyelenggara</label>
                        <input type="text" class="form-control @error('penyelenggara') is-invalid @enderror" id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara', 'Pemerintah Kecamatan Soreang') }}" placeholder="Pemerintah Kecamatan Soreang">
                        @error('penyelenggara')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="lokasi">Lokasi Kegiatan</label>
                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Aula Kantor Kecamatan Soreang / Kelurahan Watang Soreang">
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="mulai_at">Waktu Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('mulai_at') is-invalid @enderror" id="mulai_at" name="mulai_at" value="{{ old('mulai_at', date('Y-m-d\TH:i')) }}" required>
                        @error('mulai_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="selesai_at">Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" class="form-control @error('selesai_at') is-invalid @enderror" id="selesai_at" name="selesai_at" value="{{ old('selesai_at') }}">
                        @error('selesai_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="waktu_keterangan">Keterangan Jam (Teks)</label>
                        <input type="text" class="form-control @error('waktu_keterangan') is-invalid @enderror" id="waktu_keterangan" name="waktu_keterangan" value="{{ old('waktu_keterangan') }}" placeholder="Contoh: 08:30 WITA - Selesai">
                        @error('waktu_keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="ringkasan">Ringkasan Singkat</label>
                        <textarea class="form-control @error('ringkasan') is-invalid @enderror" id="ringkasan" name="ringkasan" rows="2" placeholder="Tuliskan ringkasan singkat kegiatan (1-2 kalimat)...">{{ old('ringkasan') }}</textarea>
                        @error('ringkasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="deskripsi">Deskripsi Detail</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" placeholder="Detail susunan acara atau penjelasan lengkap kegiatan...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="gambar">File Gambar Poster / Banner</label>
                        <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, WEBP. Maks: 3MB.</small>
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="file_lampiran">File Lampiran PDF / Dokumen (Opsional)</label>
                        <input type="file" class="form-control @error('file_lampiran') is-invalid @enderror" id="file_lampiran" name="file_lampiran" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip">
                        <small class="text-muted">Format: PDF, Word, Excel, ZIP. Maks: 5MB.</small>
                        @error('file_lampiran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_published">Publikasikan Langsung ke Landing Page</label>
                        </div>
                    </div>

                    <div class="col-md-12 border-top pt-4 mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-save-line me-1"></i> Simpan Agenda
                        </button>
                        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
