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
                            @php
                                $fieldDefs = collect($data->detail->jenisSurat->fields ?? []);
                                $fieldLabels = $fieldDefs->pluck('label', 'name');
                            @endphp
                            <tr>
                                <th class="text-muted">Jenis Layanan</th>
                                <td>: Persuratan - {{ $data->detail->jenisSurat->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Keperluan</th>
                                <td>: {{ $data->detail->keperluan }}</td>
                            </tr>
                            @foreach ($data->detail->data ?? [] as $fieldName => $fieldValue)
                                <tr>
                                    <th class="text-muted">{{ $fieldLabels[$fieldName] ?? \Illuminate\Support\Str::headline($fieldName) }}</th>
                                    <td>: {{ is_array($fieldValue) ? implode(', ', $fieldValue) : $fieldValue }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th class="text-muted">Nomor Surat</th>
                                <td>:
                                    @if ($data->detail->nomor_surat)
                                        <code>{{ $data->detail->nomor_surat }}</code>
                                    @else
                                        <span class="text-muted">Belum terbit (terbit saat tiket selesai)</span>
                                    @endif
                                </td>
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

            @if ($data->detail && str_contains(get_class($data->detail), 'PengajuanSurat'))
                @php
                    $lampiranGrouped = $data->detail->media
                        ->where('collection', '!=', \App\Services\SuratService::PDF_COLLECTION)
                        ->groupBy('collection');
                    $suratPdf = $data->detail->media
                        ->where('collection', \App\Services\SuratService::PDF_COLLECTION)
                        ->sortByDesc('id')
                        ->first();
                @endphp

                <div class="card p-4 mt-4">
                    <h6 class="fw-bold mb-3">Lampiran Syarat</h6>
                    @forelse ($lampiranGrouped as $collection => $mediaList)
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">{{ $fieldLabels[$collection] ?? \Illuminate\Support\Str::headline($collection) }}</small>
                            @foreach ($mediaList as $media)
                                <a href="{{ route('tiket.lampiran', [$data->id, $media->id]) }}" class="d-flex align-items-center text-truncate mb-1">
                                    <i class="ri-attachment-2 me-1"></i>
                                    {{ $media->original_name }}
                                    <span class="text-muted ms-1">({{ $media->size_formatted }})</span>
                                </a>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-muted mb-0">Tidak ada lampiran.</p>
                    @endforelse
                </div>

                <div class="card p-4 mt-4">
                    <h6 class="fw-bold mb-3">Surat Resmi (PDF)</h6>
                    @if ($suratPdf)
                        <a href="{{ route('tiket.surat-pdf', $data->id) }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="ri-file-download-line me-1"></i> Unduh PDF Surat
                        </a>
                    @endif

                    @if ($data->status->value === 'selesai' && $data->detail->nomor_surat)
                        <form action="{{ route('tiket.regenerate-surat-pdf', $data->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="ri-refresh-line me-1"></i> {{ $suratPdf ? 'Generate Ulang PDF' : 'Generate PDF Surat' }}
                            </button>
                        </form>
                    @elseif (! $suratPdf)
                        <p class="text-muted mb-0">PDF dibuat otomatis saat tiket ditandai selesai.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
