@extends('layouts/layoutMaster')

@section('title', 'Tambah Jenis Surat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('jenis-surat.index') }}">Jenis Surat</a> /</span> Tambah
    </h4>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Jenis Surat</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jenis-surat.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kode">Kode</label>
                                <input type="text" class="form-control text-uppercase @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode') }}" maxlength="20" placeholder="SKET" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="nama">Nama Jenis Surat</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Surat Keterangan" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="wajib_pengantar_rt_rw" name="wajib_pengantar_rt_rw" value="1" {{ old('wajib_pengantar_rt_rw', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="wajib_pengantar_rt_rw">Wajib Sertakan Pengantar RT/RW</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Jenis Surat</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                            <a href="{{ route('jenis-surat.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
