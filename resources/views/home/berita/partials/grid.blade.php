<div id="beritaGridContent" class="row gy-4">
  @forelse($beritaList as $index => $b)
    @php
      $imgPath = $b->thumbnail ?? $b->gambar ?? null;
      if ($imgPath && !Str::startsWith($imgPath, 'http')) {
          $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
      }
      $tanggal = is_object($b->published_at ?? null) 
          ? $b->published_at->format('d M Y') 
          : (is_object($b->created_at ?? null) ? $b->created_at->format('d M Y') : date('d M Y'));
    @endphp

    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index % 3 + 1) * 100 }}">
      <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift transition-all bg-white d-flex flex-column">
        <div class="position-relative overflow-hidden bg-dark" style="height: 220px;">
          @if(!empty($imgPath))
            <img src="{{ $imgPath }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $b->judul }}">
          @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
              <i class="bi bi-newspaper display-4 opacity-50"></i>
            </div>
          @endif
          <span class="position-absolute top-0 end-0 m-3 badge bg-primary shadow-sm rounded-pill px-3 py-2 fs-7">
            {{ $b->kategori ?? 'Umum' }}
          </span>
        </div>
        
        <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
          <div>
            <div class="d-flex align-items-center gap-3 text-muted small mb-2">
              <span><i class="bi bi-calendar3 me-1 text-primary"></i> {{ $tanggal }}</span>
              <span><i class="bi bi-person-circle me-1 text-secondary"></i> {{ $b->penulis ?? 'Admin Soreang' }}</span>
            </div>

            <h5 class="card-title fw-bold text-dark fs-6 mb-2 leading-snug">
              <a href="{{ route('berita.public.show', $b->slug ?? $b->id) }}" class="text-dark text-decoration-none hover-primary">
                {{ $b->judul }}
              </a>
            </h5>

            <p class="card-text text-secondary small line-clamp-3 mb-0" style="line-height: 1.6;">
              {{ Str::limit(strip_tags($b->ringkasan ?? $b->isi ?? ''), 120) }}
            </p>
          </div>

          <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('berita.public.show', $b->slug ?? $b->id) }}" class="fw-semibold text-primary text-decoration-none small d-inline-flex align-items-center gap-1 hover-gap">
              Baca Artikel <i class="bi bi-arrow-right ms-1"></i>
            </a>
            <small class="text-muted"><i class="bi bi-clock me-1"></i> Informasi Publik</small>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-5">
      <div class="p-5 bg-white rounded-4 border shadow-sm max-w-md mx-auto">
        <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
          <i class="bi bi-newspaper"></i>
        </div>
        <h5 class="fw-bold text-dark mb-2">Tidak Ada Berita Ditemukan</h5>
        <p class="text-muted small mb-0">Maaf, berita atau pengumuman yang Anda cari tidak ditemukan untuk kriteria filter yang Anda pilih.</p>
      </div>
    </div>
  @endforelse
</div>

@if($beritaList->hasPages())
  <div class="d-flex justify-content-center mt-5">
    {{ $beritaList->links('pagination::bootstrap-5') }}
  </div>
@endif
