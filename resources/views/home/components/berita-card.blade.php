@props([
    'berita',
    'delay' => 100
])

@php
  $imgPath = $berita->thumbnail ?? $berita->gambar ?? null;
  if ($imgPath && !Str::startsWith($imgPath, 'http')) {
      $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
  }
  $tanggal = is_object($berita->published_at ?? null) 
      ? $berita->published_at->format('d M Y') 
      : (is_object($berita->created_at ?? null) ? $berita->created_at->format('d M Y') : date('d M Y'));
@endphp

<div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="{{ $delay }}">
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden w-100 h-100 hover-lift transition-all">
    <div class="position-relative" style="height: 190px; background-color: #f1f5f9;">
      @if(!empty($imgPath))
        <img src="{{ $imgPath }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $berita->judul }}">
      @else
        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
          <i class="bi bi-newspaper display-4 opacity-50"></i>
        </div>
      @endif
      <span class="position-absolute top-0 end-0 m-3 badge bg-primary shadow-sm rounded-pill px-3 py-2">
        {{ $berita->kategori ?? 'Umum' }}
      </span>
    </div>
    
    <div class="card-body p-4 d-flex flex-column">
      <div class="d-flex align-items-center gap-3 text-muted small mb-2">
        <span><i class="bi bi-calendar3 me-1 text-primary"></i> {{ $tanggal }}</span>
        <span><i class="bi bi-person-circle me-1 text-primary"></i> {{ $berita->penulis ?? 'Admin Soreang' }}</span>
      </div>

      <h5 class="card-title fw-bold text-dark fs-6 mb-2 line-clamp-2" style="line-height: 1.4;">
        {{ $berita->judul }}
      </h5>

      <p class="card-text text-muted small mb-4 flex-grow-1 line-clamp-3">
        {{ Str::limit(strip_tags($berita->ringkasan ?? $berita->konten ?? ''), 110) }}
      </p>

      <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
        <a href="javascript:void(0)" class="fw-semibold text-primary text-decoration-none small d-flex align-items-center">
          Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
        </a>
        <small class="text-muted"><i class="bi bi-eye me-1"></i> Public</small>
      </div>
    </div>
  </div>
</div>
