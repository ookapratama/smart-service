@extends('layouts/layoutMaster')

@section('title', 'Detail Agenda')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Agenda /</span> Detail Agenda
        </h4>
        <div>
            <a href="{{ route('agenda.edit', $data->id) }}" class="btn btn-primary me-2">
                <i class="ri-pencil-line me-1"></i> Edit
            </a>
            <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row gy-4">
                <div class="col-md-5 text-center">
                    @php
                        $imgPath = $data->gambar;
                        if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                            $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                        }
                    @endphp
                    @if($imgPath)
                        <img src="{{ $imgPath }}" alt="{{ $data->judul }}" class="img-fluid rounded border shadow-sm w-100 object-fit-cover" style="max-height: 320px;">
                    @else
                        <div class="bg-light rounded p-5 text-muted border">
                            <i class="ri-calendar-event-line ri-4x"></i>
                            <p class="mt-2 m-0">Tidak ada poster/gambar</p>
                        </div>
                    @endif
                </div>

                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-label-info">{{ $data->kategori }}</span>
                        @if(optional($data->mulai_at)->isFuture())
                            <span class="badge bg-warning">Mendatang</span>
                        @else
                            <span class="badge bg-success">Selesai</span>
                        @endif
                        @if($data->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>

                    <h3 class="fw-bold text-dark mb-3">{{ $data->judul }}</h3>

                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-2 text-dark small">
                            <div class="col-md-6"><i class="ri-calendar-line text-primary me-1"></i> <strong>Waktu Mulai:</strong> {{ optional($data->mulai_at)->format('d M Y H:i') }} WITA</div>
                            <div class="col-md-6"><i class="ri-map-pin-line text-danger me-1"></i> <strong>Lokasi:</strong> {{ $data->lokasi ?? 'Kantor Kecamatan Soreang' }}</div>
                            <div class="col-md-6"><i class="ri-user-star-line text-info me-1"></i> <strong>Penyelenggara:</strong> {{ $data->penyelenggara ?? 'Kecamatan Soreang' }}</div>
                            <div class="col-md-6"><i class="ri-time-line text-warning me-1"></i> <strong>Jam:</strong> {{ $data->waktu_keterangan ?? 'Sesuai Jadwal' }}</div>
                        </div>
                    </div>

                    @if($data->ringkasan)
                        <h6 class="fw-bold mb-1">Ringkasan:</h6>
                        <p class="text-muted small mb-3">{{ $data->ringkasan }}</p>
                    @endif

                    <h6 class="fw-bold mb-2">Deskripsi Detail:</h6>
                    <p class="text-secondary leading-relaxed mb-3">{{ $data->deskripsi ?? 'Tidak ada deskripsi detail.' }}</p>

                    @if($data->file_lampiran)
                        @php
                            $docPath = Str::startsWith($data->file_lampiran, 'storage/') ? asset($data->file_lampiran) : asset('storage/' . ltrim($data->file_lampiran, '/'));
                        @endphp
                        <div class="alert alert-info d-flex align-items-center justify-content-between mb-0" role="alert">
                            <span><i class="ri-file-download-line me-1"></i> File Lampiran Agenda Kegiatan</span>
                            <a href="{{ $docPath }}" target="_blank" class="btn btn-sm btn-info">Unduh Berkas</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
