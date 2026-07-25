@extends('layouts/layoutMaster')

@section('title', 'Tambah Instansi')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Wilayah / <a href="{{ route('instansi.index') }}">Instansi</a> /</span> Tambah
    </h4>

    <div class="row">
        <div class="mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-1">Cari dari Data Wilayah (opsional)</h6>
                    <small class="text-muted">Pilih Provinsi &rarr; Kabupaten/Kota &rarr; Kecamatan untuk mengisi Nama & Kode otomatis dari data resmi wilayah.id. Data Kelurahan/Desa belum disinkronkan — isi manual untuk tingkat tersebut.</small>
                </div>
                <div class="card-body row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label" for="wilayah_provinsi">Provinsi</label>
                        <select class="select2 form-select" id="wilayah_provinsi">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label" for="wilayah_kabkota">Kabupaten/Kota</label>
                        <select class="select2 form-select" id="wilayah_kabkota" disabled>
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="wilayah_kecamatan">Kecamatan</label>
                        <select class="select2 form-select" id="wilayah_kecamatan" disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Instansi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('instansi.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="level">Tingkat</label>
                                <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="kabupaten" {{ old('level') == 'kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                                    <option value="kecamatan" {{ old('level') == 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                                    <option value="kelurahan" {{ old('level') == 'kelurahan' ? 'selected' : '' }}>Kelurahan/Desa</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="parent_id">Induk Instansi</label>
                                <select class="select2 form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                    <option value="">-- Tidak ada (Tingkat Tertinggi) --</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }} ({{ $parent->level->label() }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Induk yang dipilih di sini adalah instansi yang sudah terdaftar sebagai tenant, bukan hasil pencarian wilayah di atas.</small>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="name">Nama Instansi</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Kecamatan Soreang" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kode">Kode Wilayah</label>
                                <input type="text" class="form-control text-uppercase @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode') }}" maxlength="10" placeholder="SRG" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="subdomain">Subdomain</label>
                                <input type="text" class="form-control @error('subdomain') is-invalid @enderror" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" placeholder="soreang">
                                @error('subdomain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="telepon">Telepon</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon') }}">
                                @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="alamat">Alamat</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Instansi</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                            <a href="{{ route('instansi.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
@vite(['resources/assets/js/select2-init.js'])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var childrenUrlTemplate = "{{ route('wilayah.children', ':code') }}";

        function loadWilayahOptions($select, parentCode, placeholder) {
            $select.html('<option value="">' + placeholder + '</option>').prop('disabled', true);

            if (!parentCode) {
                return;
            }

            $.getJSON(childrenUrlTemplate.replace(':code', parentCode), function (items) {
                items.forEach(function (item) {
                    $select.append($('<option>', { value: item.code, text: item.name }));
                });
                $select.prop('disabled', false);
            });
        }

        loadWilayahOptions($('#wilayah_provinsi'), 'root', '-- Pilih Provinsi --');

        $('#wilayah_provinsi').on('change', function () {
            loadWilayahOptions($('#wilayah_kabkota'), $(this).val(), '-- Pilih Kabupaten/Kota --');
            loadWilayahOptions($('#wilayah_kecamatan'), null, '-- Pilih Kecamatan --');
        });

        $('#wilayah_kabkota').on('change', function () {
            var code = $(this).val();
            var name = $(this).find('option:selected').text();

            if ($('#level').val() === 'kabupaten' && code) {
                $('#name').val(name);
                $('#kode').val(code);
            }

            loadWilayahOptions($('#wilayah_kecamatan'), code, '-- Pilih Kecamatan --');
        });

        $('#wilayah_kecamatan').on('change', function () {
            var code = $(this).val();
            var name = $(this).find('option:selected').text();

            if ($('#level').val() === 'kecamatan' && code) {
                $('#name').val(name);
                $('#kode').val(code);
            }
        });
    });
</script>
@endsection
