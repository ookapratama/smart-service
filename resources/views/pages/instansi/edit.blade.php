@extends('layouts/layoutMaster')

@section('title', 'Edit Instansi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Wilayah / <a href="{{ route('instansi.index') }}">Instansi</a> /</span> Edit
    </h4>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data: {{ $data->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('instansi.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="name">Nama Instansi</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $data->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kode">Kode Wilayah</label>
                                <input type="text" class="form-control text-uppercase @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode', $data->kode) }}" maxlength="10" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="level">Tingkat</label>
                                <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="kabupaten" {{ old('level', $data->level->value) == 'kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                                    <option value="kecamatan" {{ old('level', $data->level->value) == 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                                    <option value="kelurahan" {{ old('level', $data->level->value) == 'kelurahan' ? 'selected' : '' }}>Kelurahan/Desa</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="parent_id">Induk Instansi</label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                    <option value="">-- Tidak ada (Tingkat Tertinggi) --</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', $data->parent_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }} ({{ $parent->level->label() }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="subdomain">Subdomain</label>
                                <input type="text" class="form-control @error('subdomain') is-invalid @enderror" id="subdomain" name="subdomain" value="{{ old('subdomain', $data->subdomain) }}">
                                @error('subdomain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="telepon">Telepon</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $data->telepon) }}">
                                @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="alamat">Alamat</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2">{{ old('alamat', $data->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $data->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Instansi</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Data</button>
                            <a href="{{ route('instansi.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
