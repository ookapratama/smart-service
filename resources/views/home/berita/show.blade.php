@extends('home.layouts.app')

@section('title', $berita->judul . ' - Kecamatan Soreang Kota Parepare')
@section('meta_description', Str::limit(strip_tags($berita->ringkasan ?? $berita->isi ?? ''), 150))

@section('content')

  @php
    $imgPath = $berita->thumbnail ?? $berita->gambar ?? null;
    if ($imgPath && !Str::startsWith($imgPath, 'http')) {
      $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
    }
    $tanggal = is_object($berita->published_at ?? null)
      ? $berita->published_at->format('d F Y')
      : (is_object($berita->created_at ?? null) ? $berita->created_at->format('d F Y') : date('d F Y'));
  @endphp

  <!-- BREADCRUMB & HEADER -->
  <section class="py-4 bg-light border-bottom">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb m-0 small">
          <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
          <li class="breadcrumb-item"><a href="{{ route('berita.public.index') }}" class="text-decoration-none">Portal
              Berita</a></li>
          <li class="breadcrumb-item active text-truncate max-w-xs" aria-current="page">{{ $berita->judul }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <!-- DETAIL CONTENT SECTION -->
  <section class="py-5 bg-white">
    <div class="container">
      <div class="row gy-5">

        <!-- Main Article Content -->
        <div class="col-lg-8" data-aos="fade-up">
          <article class="blog-details">

            <div class="mb-4">
              <span class="badge bg-primary rounded-pill px-3 py-2 me-2 mb-2">{{ $berita->kategori ?? 'Umum' }}</span>
              <h1 class="fw-extrabold text-dark display-6 mb-3" style="line-height: 1.3;">
                {{ $berita->judul }}
              </h1>

              <div class="d-flex align-items-center gap-3 text-muted small pb-3 border-bottom">
                <span class="d-flex align-items-center"><i class="bi bi-person-circle text-primary me-1"></i>
                  {{ $berita->penulis ?? 'Admin Soreang' }}</span>
                <span class="d-flex align-items-center"><i class="bi bi-calendar3 text-primary me-1"></i>
                  {{ $tanggal }}</span>
                <span class="d-flex align-items-center"><i class="bi bi-eye text-primary me-1"></i> Public</span>
              </div>
            </div>

            @if(!empty($imgPath))
              <div class="mb-4 rounded-4 overflow-hidden shadow-sm" style="max-height: 450px; background-color: #f1f5f9;">
                <img src="{{ $imgPath }}" class="img-fluid w-100 object-fit-cover" alt="{{ $berita->judul }}">
              </div>
            @endif

            @if($berita->ringkasan)
              <div class="p-4 bg-light rounded-4 border-start mb-4">
                <p class="fw-semibold text-dark fs-6 m-0 leading-relaxed">
                  "{{ $berita->ringkasan }}"
                </p>
              </div>
            @endif

            <div class="article-body text-dark leading-relaxed fs-6 mb-5" style="font-size: 1.05rem; line-height: 1.8;">
              @if(!empty($berita->isi))
                {!! $berita->isi !!}
              @else
                <p>{!! nl2br(e($berita->ringkasan ?? 'Konten berita belum diisi.')) !!}</p>
              @endif
            </div>

            <!-- Share Buttons -->
            <div class="p-4 bg-light rounded-4 d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
              <span class="fw-bold text-dark small"><i class="bi bi-share me-1 text-primary"></i> Bagikan Artikel
                Ini:</span>
              <div class="d-flex gap-2">
                <a href="https://api.whatsapp.com/send?text={{ urlencode($berita->judul . ' - ' . url()->current()) }}"
                  target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
                  <i class="bi bi-whatsapp me-1"></i> WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                  class="btn btn-sm btn-primary rounded-pill px-3">
                  <i class="bi bi-facebook me-1"></i> Facebook
                </a>
              </div>
            </div>

            <div class="pt-3 border-top">
              <a href="{{ route('berita.public.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Portal Berita
              </a>
            </div>

          </article>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
          <div class="p-4 bg-light rounded-4 border sticky-top" style="top: 100px; z-index: 10;">

            <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom border-primary d-inline-block">Berita Terkait</h5>

            <div class="d-flex flex-column gap-3 mb-4">
              @foreach($relatedBerita as $rel)
                @php
                  $relImg = $rel->thumbnail ?? $rel->gambar ?? null;
                  if ($relImg && !Str::startsWith($relImg, 'http')) {
                    $relImg = Str::startsWith($relImg, 'storage/') ? asset($relImg) : asset('storage/' . ltrim($relImg, '/'));
                  }
                @endphp
                <div class="d-flex gap-3 align-items-center">
                  @if(!empty($relImg))
                    <img src="{{ $relImg }}" class="rounded-3 object-fit-cover flex-shrink-0"
                      style="width: 70px; height: 70px;" alt="{{ $rel->judul }}">
                  @else
                    <div
                      class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                      style="width: 70px; height: 70px;">
                      <i class="bi bi-newspaper fs-4"></i>
                    </div>
                  @endif
                  <div>
                    <span class="badge bg-light text-primary border mb-1"
                      style="font-size: 0.7rem;">{{ $rel->kategori ?? 'Umum' }}</span>
                    <h6 class="fw-bold text-dark mb-1 line-clamp-2" style="font-size: 0.85rem; line-height: 1.3;">
                      <a href="{{ route('berita.public.show', $rel->slug ?? $rel->id) }}"
                        class="text-dark text-decoration-none hover-primary">
                        {{ $rel->judul }}
                      </a>
                    </h6>
                    <small class="text-muted"
                      style="font-size: 0.75rem;">{{ is_object($rel->published_at) ? $rel->published_at->format('d M Y') : date('d M Y') }}</small>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Banner Portal Services Widget -->
            <div class="p-4 bg-white rounded-4 border shadow-sm text-center">
              <div
                class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3 mx-auto"
                style="width: 54px; height: 54px; font-size: 1.5rem;">
                <i class="bi bi-file-earmark-check-fill"></i>
              </div>
              <h6 class="fw-bold text-dark mb-1">Butuh Surat Online?</h6>
              <p class="text-muted small mb-3" style="line-height: 1.5;">Ajukan surat administrasi kependudukan Anda
                secara instan di 3S Soreang.</p>
              <a href="{{ route('home') }}#jenis-surat-modal"
                class="btn btn-primary btn-sm rounded-pill fw-semibold px-4 w-100 shadow-sm">
                Pengajuan Surat Online
              </a>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

@endsection