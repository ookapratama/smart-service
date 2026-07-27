@extends('layouts/layoutMaster')

@section('title', 'Detail Jadwal Pelayanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('jadwal-pelayanan.index') }}">Jadwal Pelayanan</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h3 class="mb-1 text-primary">{{ $data->kelurahan }}</h3>
                        <p class="text-muted mb-0"><i class="ri-calendar-line me-1"></i> Hari Operasional: <strong>{{ $data->hari_operasional ?? 'Senin - Jumat' }}</strong></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('jadwal-pelayanan.edit', $data->id) }}" class="btn btn-primary"><i class="ri-pencil-line me-1"></i> Edit</a>
                        <a href="{{ route('jadwal-pelayanan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180" class="text-muted">Jam Buka - Tutup</th>
                                <td>: <span class="badge bg-success fs-6">{{ $data->jam_buka }} - {{ $data->jam_tutup }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Jam Istirahat</th>
                                <td>: {{ $data->istirahat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Petugas Penanggung Jawab</th>
                                <td>: {{ $data->petugas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">No. Telepon / WhatsApp</th>
                                <td>: {{ $data->telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Status Operasional</th>
                                <td>: 
                                    @if ($data->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Catatan Khusus</th>
                                <td>: {{ $data->catatan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
