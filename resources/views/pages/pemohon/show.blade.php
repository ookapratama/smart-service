@extends('layouts/layoutMaster')

@section('title', 'Detail Pemohon')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Pelayanan / <a href="{{ route('pemohon.index') }}">Pemohon</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h4 class="mb-1 text-primary">{{ $data->name }}</h4>
                        <p class="text-muted mb-0">NIK: <code>{{ $data->nik }}</code></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pemohon.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('pemohon.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150" class="text-muted">Kelurahan</th>
                                <td>: {{ $data->kelurahan->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">No. HP</th>
                                <td>: {{ $data->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email</th>
                                <td>: {{ $data->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Alamat</th>
                                <td>: {{ $data->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Terdaftar Sejak</th>
                                <td>: {{ $data->created_at->format('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($data->tikets->isNotEmpty())
                    <div class="mt-3 pt-3 border-top">
                        <h6 class="fw-bold mb-2">Riwayat Tiket</h6>
                        <ul class="list-group">
                            @foreach ($data->tikets as $tiket)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('tiket.show', $tiket->id) }}">{{ $tiket->nomor_tiket }} - {{ $tiket->judul }}</a>
                                    <span class="badge bg-label-info">{{ $tiket->status->label() }}</span>
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
