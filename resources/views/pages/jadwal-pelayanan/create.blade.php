@extends('layouts/layoutMaster')

@section('title', 'Tambah Jadwal Pelayanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('jadwal-pelayanan.index') }}">Jadwal Pelayanan</a> /</span> Tambah
    </h4>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Jadwal Pelayanan Kelurahan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jadwal-pelayanan.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="kelurahan_id">Kelurahan</label>
                                <select class="form-select @error('kelurahan_id') is-invalid @enderror" id="kelurahan_id" name="kelurahan_id" required>
                                    <option value="">-- Pilih Kelurahan --</option>
                                    @foreach ($kelurahanList as $kelurahan)
                                        <option value="{{ $kelurahan->id }}" {{ old('kelurahan_id') == $kelurahan->id ? 'selected' : '' }}>{{ $kelurahan->nama }}</option>
                                    @endforeach
                                </select>
                                @error('kelurahan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Hari Operasional</label>
                                @php $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']; $selectedHari = old('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']); @endphp
                                @foreach ($hariOptions as $hari)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="hari[]" id="hari_{{ $hari }}" value="{{ $hari }}" {{ in_array($hari, $selectedHari) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hari_{{ $hari }}">{{ $hari }}</label>
                                    </div>
                                @endforeach
                                @error('hari')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="jam_buka">Jam Buka</label>
                                <input type="text" class="form-control @error('jam_buka') is-invalid @enderror" id="jam_buka" name="jam_buka" value="{{ old('jam_buka', '08:00') }}" placeholder="08:00" required>
                                @error('jam_buka')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="jam_tutup">Jam Tutup</label>
                                <input type="text" class="form-control @error('jam_tutup') is-invalid @enderror" id="jam_tutup" name="jam_tutup" value="{{ old('jam_tutup', '15:30') }}" placeholder="15:30" required>
                                @error('jam_tutup')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="istirahat">Waktu Istirahat</label>
                                <input type="text" class="form-control @error('istirahat') is-invalid @enderror" id="istirahat" name="istirahat" value="{{ old('istirahat', '12:00 - 13:00') }}" placeholder="12:00 - 13:00">
                                @error('istirahat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="petugas">Petugas Penanggung Jawab</label>
                                <input type="text" class="form-control @error('petugas') is-invalid @enderror" id="petugas" name="petugas" value="{{ old('petugas') }}" placeholder="Contoh: Budi Santoso, S.AP">
                                @error('petugas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="telepon">No. Telepon / WhatsApp</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon') }}" placeholder="0812-3456-7890">
                                @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="keterangan">Catatan / Keterangan Khusus</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="2" placeholder="Informasi pelayanan khusus atau persyaratan kelurahan...">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Jadwal Pelayanan</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Simpan Jadwal</button>
                            <a href="{{ route('jadwal-pelayanan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
