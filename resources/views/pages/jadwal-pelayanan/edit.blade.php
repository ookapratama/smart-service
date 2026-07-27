@extends('layouts/layoutMaster')

@section('title', 'Edit Jadwal Pelayanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('jadwal-pelayanan.index') }}">Jadwal Pelayanan</a> /</span> Edit
    </h4>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Edit Jadwal Pelayanan Kelurahan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jadwal-pelayanan.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="kelurahan">Nama Kelurahan</label>
                                <input type="text" class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $data->kelurahan) }}" required>
                                @error('kelurahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="hari_operasional">Hari Operasional</label>
                                <input type="text" class="form-control @error('hari_operasional') is-invalid @enderror" id="hari_operasional" name="hari_operasional" value="{{ old('hari_operasional', $data->hari_operasional) }}">
                                @error('hari_operasional')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="jam_buka">Jam Buka</label>
                                <input type="text" class="form-control @error('jam_buka') is-invalid @enderror" id="jam_buka" name="jam_buka" value="{{ old('jam_buka', $data->jam_buka) }}" required>
                                @error('jam_buka')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="jam_tutup">Jam Tutup</label>
                                <input type="text" class="form-control @error('jam_tutup') is-invalid @enderror" id="jam_tutup" name="jam_tutup" value="{{ old('jam_tutup', $data->jam_tutup) }}" required>
                                @error('jam_tutup')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="istirahat">Waktu Istirahat</label>
                                <input type="text" class="form-control @error('istirahat') is-invalid @enderror" id="istirahat" name="istirahat" value="{{ old('istirahat', $data->istirahat) }}">
                                @error('istirahat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="petugas">Petugas Penanggung Jawab</label>
                                <input type="text" class="form-control @error('petugas') is-invalid @enderror" id="petugas" name="petugas" value="{{ old('petugas', $data->petugas) }}">
                                @error('petugas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="telepon">No. Telepon / WhatsApp</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $data->telepon) }}">
                                @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="catatan">Catatan / Keterangan Khusus</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="2">{{ old('catatan', $data->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $data->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Jadwal Pelayanan</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Perbarui Jadwal</button>
                            <a href="{{ route('jadwal-pelayanan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
