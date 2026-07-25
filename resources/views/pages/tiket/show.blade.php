@extends('layouts/layoutMaster')

@section('title', 'Detail Tiket')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Pelayanan / <a href="{{ route('tiket.index') }}">Tiket</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h4 class="mb-1 text-primary">{{ $data->judul }}</h4>
                        <p class="text-muted mb-0">
                            <code>{{ $data->nomor_tiket }}</code> ·
                            <span class="badge bg-label-secondary">{{ $data->channel->label() }}</span>
                        </p>
                    </div>
                    @php
                        $statusColor = match ($data->status->value) {
                            'baru' => 'info',
                            'diproses' => 'warning',
                            'selesai' => 'success',
                            'ditolak' => 'danger',
                        };
                    @endphp
                    <span class="badge bg-{{ $statusColor }} fs-6">{{ $data->status->label() }}</span>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th width="180" class="text-muted">Pemohon</th>
                        <td>: {{ $data->pemohon->name ?? '-' }} ({{ $data->pemohon->nik ?? '-' }})</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Keterangan</th>
                        <td>: {{ $data->keterangan ?? '-' }}</td>
                    </tr>
                    @if ($data->detail)
                        @if (str_contains(get_class($data->detail), 'PengajuanSurat'))
                            <tr>
                                <th class="text-muted">Jenis Layanan</th>
                                <td>: Persuratan - {{ $data->detail->jenisSurat->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Keperluan</th>
                                <td>: {{ $data->detail->keperluan }}</td>
                            </tr>
                        @elseif (str_contains(get_class($data->detail), 'Pengaduan'))
                            <tr>
                                <th class="text-muted">Jenis Layanan</th>
                                <td>: Pengaduan - {{ $data->detail->kategori->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Deskripsi</th>
                                <td>: {{ $data->detail->deskripsi }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Lokasi</th>
                                <td>: {{ $data->detail->lokasi ?? '-' }}</td>
                            </tr>
                        @endif
                    @endif
                    <tr>
                        <th class="text-muted">Ditugaskan Kepada</th>
                        <td>: {{ $data->assignedTo->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Dibuat Pada</th>
                        <td>: {{ $data->created_at->format('d F Y H:i') }}</td>
                    </tr>
                    @if ($data->selesai_at)
                        <tr>
                            <th class="text-muted">Selesai Pada</th>
                            <td>: {{ $data->selesai_at->format('d F Y H:i') }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="card p-4">
                <h6 class="fw-bold mb-3">Riwayat Status</h6>
                <ul class="timeline mb-0">
                    @forelse ($data->statusLogs->sortBy('created_at') as $log)
                        <li class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <strong>{{ $log->status_from?->label() ?? 'Dibuat' }}</strong>
                                    <i class="ri-arrow-right-line mx-1"></i>
                                    <strong>{{ $log->status_to->label() }}</strong>
                                </span>
                                <small class="text-muted">{{ $log->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <div class="text-muted small">
                                oleh {{ $log->user->name ?? 'Sistem' }}
                                @if ($log->catatan)
                                    &mdash; {{ $log->catatan }}
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-muted">Belum ada riwayat status.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4">
                <h6 class="fw-bold mb-3">Ubah Status</h6>

                @if (empty($data->status->transitions()))
                    <p class="text-muted mb-0">Tiket ini sudah pada status akhir ({{ $data->status->label() }}).</p>
                @else
                    <form action="{{ route('tiket.update-status', $data->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="status_to">Status Baru</label>
                            <select class="form-select @error('status_to') is-invalid @enderror" id="status_to" name="status_to" required>
                                <option value="">-- Pilih Status --</option>
                                @foreach ($data->status->transitions() as $next)
                                    <option value="{{ $next->value }}">{{ $next->label() }}</option>
                                @endforeach
                            </select>
                            @error('status_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="catatan">Catatan (opsional)</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3"></textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan Status</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
