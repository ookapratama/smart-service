@extends('layouts/layoutMaster')

@section('title', 'Detail Berita')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('berita.index') }}">Berita</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <span class="badge bg-primary mb-2">{{ $data->kategori }}</span>
                        <h3 class="mb-1 text-primary">{{ $data->judul }}</h3>
                        <p class="text-muted mb-0"><i class="ri-user-line me-1"></i> {{ $data->penulis ?? 'Admin' }} | <i class="ri-calendar-line me-1"></i> {{ $data->created_at ? $data->created_at->format('d F Y H:i') : '-' }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('berita.edit', $data->id) }}" class="btn btn-primary"><i class="ri-pencil-line me-1"></i> Edit</a>
                        <a href="{{ route('berita.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>

                @php
                    $imgPath = $data->thumbnail ?? $data->gambar;
                    if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                        $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                    }
                @endphp
                @if($imgPath)
                    <div class="mb-4 text-center">
                        <img src="{{ $imgPath }}" alt="{{ $data->judul }}" class="img-fluid rounded shadow-sm" style="max-height: 350px; object-fit: cover;">
                    </div>
                @endif

                <div class="mb-4">
                    <h6 class="fw-bold text-muted mb-2">Ringkasan:</h6>
                    <p class="lead text-secondary">{{ $data->ringkasan }}</p>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-muted mb-2">Isi Berita Lengkap:</h6>
                    <div class="p-3 bg-light rounded border text-dark">
                        {!! nl2br(e($data->konten)) !!}
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3 text-muted small">
                    <span>Status Publikasi: 
                        @if($data->is_published)
                            <span class="badge bg-success ms-1">Published</span>
                        @else
                            <span class="badge bg-secondary ms-1">Draft</span>
                        @endif
                    </span>
                    <span>Total Dilihat: <strong>{{ $data->views ?? 0 }}x</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
