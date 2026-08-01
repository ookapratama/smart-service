@extends('home.layouts.app')

@section('title', $agenda->judul . ' - Agenda ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('meta_description', str($agenda->ringkasan ?? $agenda->deskripsi ?? $agenda->judul)->limit(150))

@section('content')

  <!-- HERO DETAIL -->
  <section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.92) 0%, rgba(4, 18, 55, 0.95) 100%);">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center">
        <div class="col-lg-10" data-aos="fade-up">
          <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('agenda.public.index') }}" class="text-white-50 text-decoration-none small hover-white">
              <i class="bi bi-arrow-left me-1"></i> Kembali ke Agenda
            </a>
            <span class="text-white-50">•</span>
            <span class="badge bg-primary rounded-pill px-3 py-1 fs-7">{{ $agenda->kategori }}</span>
            @if(optional($agenda->mulai_at)->isPast())
              <span class="badge bg-secondary rounded-pill px-3 py-1 fs-7">Selesai</span>
            @else
              <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fs-7">Mendatang</span>
            @endif
          </div>

          <h1 class="display-5 fw-extrabold mb-3 text-white">{{ $agenda->judul }}</h1>

          <div class="d-flex flex-wrap align-items-center text-white-50 small gap-4">
            <span><i class="bi bi-calendar3 me-1 text-primary"></i> {{ optional($agenda->mulai_at)->format('d M Y H:i') }} WITA</span>
            <span><i class="bi bi-geo-alt me-1 text-danger"></i> {{ $agenda->lokasi ?? 'Kantor Kecamatan Soreang' }}</span>
            <span><i class="bi bi-eye me-1 text-info"></i> {{ $agenda->views ?? 0 }} Dilihat</span>
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
            <div class="row g-4 mb-4 pb-4 border-bottom">
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-4 border h-100">
                  <span class="d-block text-muted small fw-semibold mb-1"><i class="bi bi-calendar-event text-primary me-1"></i> Jadwal Pelaksanaan</span>
                  <h5 class="fw-bold text-dark mb-1">{{ optional($agenda->mulai_at)->format('d F Y') }}</h5>
                  <p class="text-secondary small m-0">{{ $agenda->waktu_keterangan ?: optional($agenda->mulai_at)->format('H:i') . ' WITA - Selesai' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-4 border h-100">
                  <span class="d-block text-muted small fw-semibold mb-1"><i class="bi bi-geo-alt text-danger me-1"></i> Lokasi & Penyelenggara</span>
                  <h5 class="fw-bold text-dark mb-1">{{ $agenda->lokasi ?? 'Kantor Kecamatan Soreang' }}</h5>
                  <p class="text-secondary small m-0">Oleh: {{ $agenda->penyelenggara ?? 'Pemerintah Kecamatan Soreang' }}</p>
                </div>
              </div>
            </div>

            @php
                $imgPath = $agenda->gambar;
                if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                    $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                }
            @endphp

            @if($imgPath)
              <div class="text-center mb-4">
                <img src="{{ $imgPath }}" class="img-fluid rounded-4 shadow-sm w-100 object-fit-cover" alt="{{ $agenda->judul }}" style="max-height: 480px;">
              </div>
            @endif

            @if($agenda->ringkasan)
              <div class="p-3 bg-primary-subtle text-primary rounded-3 border border-primary-subtle mb-4 fw-semibold small">
                {{ $agenda->ringkasan }}
              </div>
            @endif

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Deskripsi & Susunan Agenda</h5>
            <div class="text-secondary leading-relaxed fs-6 mb-4">
              {!! nl2br(e($agenda->deskripsi ?? 'Tidak ada deskripsi rincian.')) !!}
            </div>

            @if($agenda->file_lampiran)
              @php
                  $docPath = Str::startsWith($agenda->file_lampiran, 'storage/') ? asset($agenda->file_lampiran) : asset('storage/' . ltrim($agenda->file_lampiran, '/'));
              @endphp
              <div class="alert alert-info d-flex align-items-center justify-content-between rounded-4 m-0" role="alert">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-file-earmark-arrow-down fs-4 text-info"></i>
                  <div>
                    <strong class="d-block text-dark">File Lampiran Agenda</strong>
                    <small class="text-muted">Unduh surat undangan / susunan acara resmi PDF/Office.</small>
                  </div>
                </div>
                <a href="{{ $docPath }}" target="_blank" class="btn btn-info text-white rounded-pill px-4 py-2">
                  <i class="bi bi-download me-1"></i> Unduh Lampiran
                </a>
              </div>
            @endif
          </div>

          <!-- RELATED AGENDA -->
          @if($relatedAgenda->count() > 0)
            <div class="mt-5">
              <h4 class="fw-bold text-dark mb-4">Agenda Kegiatan Lainnya</h4>
              <div class="row gy-4">
                @foreach($relatedAgenda as $rel)
                  <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift bg-white">
                      <div class="card-body p-4">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small mb-2">{{ $rel->kategori }}</span>
                        <h6 class="fw-bold text-dark fs-6 mb-2 line-clamp-2">
                          <a href="{{ route('agenda.public.show', $rel->slug ?: $rel->id) }}" class="text-dark text-decoration-none">
                            {{ $rel->judul }}
                          </a>
                        </h6>
                        <small class="text-muted d-block mb-1"><i class="bi bi-calendar3 me-1"></i> {{ optional($rel->mulai_at)->format('d M Y H:i') }}</small>
                        <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i> {{ $rel->lokasi ?? 'Kecamatan Soreang' }}</small>
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
