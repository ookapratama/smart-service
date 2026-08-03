{{--
  Builder field formulir dinamis untuk jenis_surat.fields (JSON).
  Ekspektasi: $fieldsValue = array definisi field (old() sudah diprioritaskan pemanggil).
--}}
@php
    $fieldErrorMessages = collect($errors->getMessages())
        ->filter(fn ($messages, $key) => str_starts_with($key, 'fields'))
        ->flatten();
@endphp

<div class="col-md-12 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0 fw-semibold">Field Formulir Warga</label>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahField">
            <i class="ri-add-line me-1"></i> Tambah Field
        </button>
    </div>
    <small class="text-muted d-block mb-2">
        Field di bawah ini menjadi isian formulir pengajuan di portal warga. Gunakan tipe
        <strong>Berkas (upload)</strong> untuk lampiran wajib seperti scan pengantar RT/RW.
    </small>

    @if ($fieldErrorMessages->isNotEmpty())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 ps-3 small">
                @foreach ($fieldErrorMessages as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="fieldBuilder">
        @foreach ($fieldsValue as $i => $field)
            <div class="card border shadow-none p-3 mb-2 field-row">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Nama (kode)</label>
                        <input type="text" name="fields[{{ $i }}][name]" class="form-control form-control-sm"
                            value="{{ $field['name'] ?? '' }}" placeholder="alamat_domisili">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Label</label>
                        <input type="text" name="fields[{{ $i }}][label]" class="form-control form-control-sm"
                            value="{{ $field['label'] ?? '' }}" placeholder="Alamat Domisili">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Tipe</label>
                        <select name="fields[{{ $i }}][type]" class="form-select form-select-sm field-type">
                            @foreach (['text' => 'Teks', 'textarea' => 'Teks Panjang', 'number' => 'Angka', 'date' => 'Tanggal', 'select' => 'Pilihan', 'file' => 'Berkas (upload)'] as $typeValue => $typeLabel)
                                <option value="{{ $typeValue }}" {{ ($field['type'] ?? 'text') === $typeValue ? 'selected' : '' }}>{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" name="fields[{{ $i }}][required]" value="1"
                                {{ ($field['required'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small">Wajib</label>
                        </div>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-field" title="Hapus field">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                    <div class="col-md-12 field-options {{ ($field['type'] ?? '') === 'select' ? '' : 'd-none' }}">
                        <label class="form-label small mb-1">Opsi pilihan (satu per baris)</label>
                        <textarea name="fields[{{ $i }}][options]" class="form-control form-control-sm" rows="2">{{ implode("\n", $field['options'] ?? []) }}</textarea>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <template id="fieldRowTemplate">
        <div class="card border shadow-none p-3 mb-2 field-row">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Nama (kode)</label>
                    <input type="text" name="fields[__IDX__][name]" class="form-control form-control-sm" placeholder="alamat_domisili">
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Label</label>
                    <input type="text" name="fields[__IDX__][label]" class="form-control form-control-sm" placeholder="Alamat Domisili">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipe</label>
                    <select name="fields[__IDX__][type]" class="form-select form-select-sm field-type">
                        <option value="text">Teks</option>
                        <option value="textarea">Teks Panjang</option>
                        <option value="number">Angka</option>
                        <option value="date">Tanggal</option>
                        <option value="select">Pilihan</option>
                        <option value="file">Berkas (upload)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" name="fields[__IDX__][required]" value="1">
                        <label class="form-check-label small">Wajib</label>
                    </div>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-field" title="Hapus field">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
                <div class="col-md-12 field-options d-none">
                    <label class="form-label small mb-1">Opsi pilihan (satu per baris)</label>
                    <textarea name="fields[__IDX__][options]" class="form-control form-control-sm" rows="2"></textarea>
                </div>
            </div>
        </div>
    </template>
</div>

@section('page-script')
<script>
(function () {
    var builder = document.getElementById('fieldBuilder');
    var template = document.getElementById('fieldRowTemplate');
    var nextIndex = {{ count($fieldsValue) }};

    document.getElementById('btnTambahField').addEventListener('click', function () {
        var html = template.innerHTML.replaceAll('__IDX__', String(nextIndex++));
        var wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        builder.appendChild(wrapper.firstElementChild);
    });

    builder.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-hapus-field');
        if (btn) {
            btn.closest('.field-row').remove();
        }
    });

    builder.addEventListener('change', function (e) {
        if (!e.target.classList.contains('field-type')) return;
        var optionsBox = e.target.closest('.field-row').querySelector('.field-options');
        optionsBox.classList.toggle('d-none', e.target.value !== 'select');
    });
})();
</script>
@endsection
