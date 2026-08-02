@extends('home.layouts.app')

@section('title', $galeri->judul . ' - Galeri ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('meta_description', str($galeri->keterangan ?? $galeri->judul)->limit(150))

@section('content')

  <!-- HERO DETAIL -->
  <section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.92) 0%, rgba(4, 18, 55, 0.95) 100%);">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center">
        <div class="col-lg-10" data-aos="fade-up">
          <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('galeri.public.index') }}" class="text-white-50 text-decoration-none small hover-white">
              <i class="bi bi-arrow-left me-1"></i> Kembali ke Galeri
            </a>
            <span class="text-white-50">•</span>
            <span class="badge bg-primary rounded-pill px-3 py-1 fs-7">{{ $galeri->kategori }}</span>
          </div>

          <h1 class="display-5 fw-extrabold mb-3 text-white">{{ $galeri->judul }}</h1>

          <div class="d-flex flex-wrap align-items-center text-white-50 small gap-4">
            <span><i class="bi bi-calendar3 me-1 text-primary"></i> {{ optional($galeri->tgl_kegiatan)->format('d M Y') ?? $galeri->created_at->format('d M Y') }}</span>
            <span><i class="bi bi-eye me-1 text-info"></i> {{ $galeri->views ?? 0 }} Dilihat</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTENT DETAIL -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row justify-content-center gy-4">
        <div class="col-lg-10">
          
          <div class="p-4 p-md-5 bg-white rounded-4 border shadow-sm mb-5">
            @php
                $imgPath = $galeri->gambar;
                if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                    $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                }
            @endphp

            @if($galeri->tipe === 'video' && $galeri->video_url)
                <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg mb-4">
                    @php
                        $videoEmbedUrl = $galeri->video_url;
                        if (str_contains($videoEmbedUrl, 'watch?v=')) {
                            $videoEmbedUrl = str_replace('watch?v=', 'embed/', $videoEmbedUrl);
                        }
                    @endphp
                    <iframe src="{{ $videoEmbedUrl }}" title="{{ $galeri->judul }}" allowfullscreen></iframe>
                </div>
            @elseif($imgPath)
                <div class="text-center mb-4">
                    <a href="{{ $imgPath }}" class="glightbox">
                        <img src="{{ $imgPath }}" class="img-fluid rounded-4 shadow-sm w-100 object-fit-cover" alt="{{ $galeri->judul }}" style="max-height: 520px;">
                    </a>
                </div>
            @endif

            @if($galeri->keterangan)
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Keterangan Dokumentasi</h5>
                <div class="text-secondary leading-relaxed fs-6">
                    {!! nl2br(e($galeri->keterangan)) !!}
                </div>
            @endif
          </div>

          <!-- RELATED MEDIA -->
          @if($relatedGaleri->count() > 0)
            <div class="mt-5">
              <h4 class="fw-bold text-dark mb-4">Dokumentasi Lainnya</h4>
              <div class="row gy-4">
                @foreach($relatedGaleri as $rel)
                  @php
                      $relImg = $rel->gambar;
                      if ($relImg && !Str::startsWith($relImg, 'http')) {
                          $relImg = Str::startsWith($relImg, 'storage/') ? asset($relImg) : asset('storage/' . ltrim($relImg, '/'));
                      }
                      if (!$relImg) {
                          $relImg = asset('assets/home/img/soreang-hero.png');
                      }
                  @endphp
                  <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift bg-white">
                      <img src="{{ $relImg }}" class="card-img-top w-100" style="height: 160px; object-fit: cover;" alt="{{ $rel->judul }}">
                      <div class="card-body p-3">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small mb-2">{{ $rel->kategori }}</span>
                        <h6 class="fw-bold text-dark fs-6 mb-1 line-clamp-2">
                          <a href="{{ route('galeri.public.show', $rel->slug ?: $rel->id) }}" class="text-dark text-decoration-none">
                            {{ $rel->judul }}
                          </a>
                        </h6>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>
  </section>

@endsection
