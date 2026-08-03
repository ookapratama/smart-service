@extends('layouts/layoutMaster')

@section('title', 'Monitoring Tiket')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Pelayanan /</span> Monitoring Tiket
        </h4>
    </div>

    <div class="card">
        <div class="card-header border-bottom pb-0">
            <ul class="nav nav-tabs card-header-tabs">
                @foreach ([null => 'Semua', 'persuratan' => 'Persuratan', 'pengaduan' => 'Pengaduan'] as $tabKey => $tabLabel)
                    <li class="nav-item">
                        <a class="nav-link {{ ($tab ?? null) === $tabKey ? 'active' : '' }}"
                           href="{{ route('tiket.index', array_filter(['tab' => $tabKey, 'status' => $filters['status'] ?? null, 'channel' => $filters['channel'] ?? null, 'q' => $filters['q'] ?? null])) }}">
                            {{ $tabLabel }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-header border-bottom">
            <form method="GET" action="{{ route('tiket.index') }}" class="row g-2 align-items-end">
                @if ($tab)
                    <input type="hidden" name="tab" value="{{ $tab }}">
                @endif
                <div class="col-md-4">
                    <label class="form-label mb-1">Cari</label>
                    <input type="search" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                        placeholder="Nomor tiket / nama / NIK pemohon">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach (['baru', 'diproses', 'selesai', 'ditolak'] as $status)
                            <option value="{{ $status }}" {{ ($filters['status'] ?? '') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Channel</label>
                    <select name="channel" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Channel</option>
                        @foreach (['web', 'whatsapp', 'qr', 'mobile', 'walkin'] as $channel)
                            <option value="{{ $channel }}" {{ ($filters['channel'] ?? '') == $channel ? 'selected' : '' }}>
                                {{ ucfirst($channel) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if (($tab ?? null) === 'persuratan')
                    <div class="col-md-2">
                        <label class="form-label mb-1">Jenis Surat</label>
                        <select name="jenis_surat_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenis</option>
                            @foreach ($jenisSuratList as $jenis)
                                <option value="{{ $jenis->id }}" {{ ($filters['jenis_surat_id'] ?? '') == $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="ri-search-line"></i></button>
                    @if (array_filter($filters))
                        <a href="{{ route('tiket.index', array_filter(['tab' => $tab])) }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Nomor Tiket</th>
                        <th>Judul</th>
                        <th>Pemohon</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td>{{ $data->firstItem() + $loop->index }}</td>
                            <td><code>{{ $item->nomor_tiket }}</code></td>
                            <td>{{ $item->judul }}</td>
                            <td>{{ $item->pemohon->name ?? '-' }}</td>
                            <td><span class="badge bg-label-secondary">{{ $item->channel->label() }}</span></td>
                            <td>
                                @php
                                    $statusColor = match ($item->status->value) {
                                        'baru' => 'info',
                                        'diproses' => 'warning',
                                        'selesai' => 'success',
                                        'ditolak' => 'danger',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ $item->status->label() }}</span>
                            </td>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('tiket.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-ticket-2-line ri-3x mb-2"></i>
                                    <p>Belum ada tiket yang masuk.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }} dari {{ $data->total() }} tiket
                </small>
                {{ $data->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
