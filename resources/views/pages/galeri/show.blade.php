@extends('layouts/layoutMaster')

@section('title', 'Detail Galeri')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Galeri /</span> Detail Galeri
        </h4>
        <div>
            <a href="{{ route('galeri.edit', $data->id) }}" class="btn btn-primary me-2">
                <i class="ri-pencil-line me-1"></i> Edit
            </a>
            <a href="{{ route('galeri.index') }}" class="btn btn-outline-secondary">
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
                            <i class="ri-image-line ri-4x"></i>
                            <p class="mt-2 m-0">Tidak ada gambar</p>
                        </div>
                    @endif
                </div>

                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-label-info">{{ $data->kategori }}</span>
                        @if($data->tipe === 'video')
                            <span class="badge bg-label-warning"><i class="ri-video-line me-1"></i> Video</span>
                        @else
                            <span class="badge bg-label-primary"><i class="ri-image-line me-1"></i> Foto</span>
                        @endif
                        @if($data->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>

                    <h3 class="fw-bold text-dark mb-3">{{ $data->judul }}</h3>

                    <div class="text-muted small mb-4">
                        <span class="me-3"><i class="ri-calendar-line me-1"></i> {{ optional($data->tgl_kegiatan)->format('d M Y') ?? $data->created_at->format('d M Y') }}</span>
                        <span><i class="ri-eye-line me-1"></i> {{ $data->views ?? 0 }} views</span>
                    </div>

                    @if($data->video_url)
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="ri-video-line me-1"></i> <strong>URL Video:</strong> 
                            <a href="{{ $data->video_url }}" target="_blank" class="text-decoration-underline">{{ $data->video_url }}</a>
                        </div>
                    @endif

                    <h6 class="fw-bold mb-2">Keterangan:</h6>
                    <p class="text-secondary leading-relaxed">{{ $data->keterangan ?? 'Tidak ada keterangan.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
