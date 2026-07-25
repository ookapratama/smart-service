@extends('layouts/layoutMaster')

@section('title', 'Detail Instansi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Wilayah / <a href="{{ route('instansi.index') }}">Instansi</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h4 class="mb-1 text-primary">{{ $data->name }}</h4>
                        <p class="text-muted mb-0">
                            <span class="badge bg-label-info">{{ $data->level->label() }}</span>
                            <code>{{ $data->kode }}</code>
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('instansi.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('instansi.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150" class="text-muted">Induk Instansi</th>
                                <td>: {{ $data->parent->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Subdomain</th>
                                <td>: {{ $data->subdomain ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Telepon</th>
                                <td>: {{ $data->telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Alamat</th>
                                <td>: {{ $data->alamat ?? '-' }}</td>
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
                                <th class="text-muted">Dibuat Pada</th>
                                <td>: {{ $data->created_at->format('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($data->children->isNotEmpty())
                    <div class="mt-3 pt-3 border-top">
                        <h6 class="fw-bold mb-2">Instansi di Bawahnya</h6>
                        <ul class="list-group">
                            @foreach ($data->children as $child)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('instansi.show', $child->id) }}">{{ $child->name }}</a>
                                    <span class="badge bg-label-info">{{ $child->level->label() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
