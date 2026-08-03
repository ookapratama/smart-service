@extends('layouts/layoutMaster')

@section('title', 'Detail Jenis Surat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('jenis-surat.index') }}">Jenis Surat</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h4 class="mb-1 text-primary">{{ $data->nama }}</h4>
                        <p class="text-muted mb-0"><code>{{ $data->kode }}</code></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('jenis-surat.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('jenis-surat.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180" class="text-muted">Deskripsi</th>
                                <td>: {{ $data->deskripsi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Status</th>
                                <td>:
                                    @if ($data->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Template PDF</th>
                                <td>: <code>{{ $data->template_view ?: 'otomatis (kode / generik)' }}</code></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Dibuat Pada</th>
                                <td>: {{ $data->created_at->format('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h6 class="fw-bold mt-2 mb-2">Field Formulir Warga</h6>
                @if (empty($data->fields))
                    <p class="text-muted mb-0">Belum ada field — formulir warga hanya menampilkan blok identitas + keperluan.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama (kode)</th>
                                    <th>Label</th>
                                    <th>Tipe</th>
                                    <th>Wajib</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->fields as $field)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><code>{{ $field['name'] ?? '-' }}</code></td>
                                        <td>{{ $field['label'] ?? '-' }}</td>
                                        <td><span class="badge bg-label-primary">{{ $field['type'] ?? 'text' }}</span></td>
                                        <td>{{ ($field['required'] ?? false) ? 'Ya' : 'Tidak' }}</td>
                                        <td>{{ implode(', ', $field['options'] ?? []) ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
