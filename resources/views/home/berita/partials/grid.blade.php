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

    <div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="{{ ($index % 3 + 1) * 100 }}">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden w-100 h-100 hover-lift transition-all bg-white">
        <div class="position-relative" style="height: 200px; background-color: #e2e8f0;">
          @if(!empty($imgPath))
            <img src="{{ $imgPath }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $b->judul }}">
          @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
              <i class="bi bi-newspaper display-4 opacity-50"></i>
            </div>
          @endif
          <span class="position-absolute top-0 end-0 m-3 badge bg-primary shadow-sm rounded-pill px-3 py-2">
            {{ $b->kategori ?? 'Umum' }}
          </span>
        </div>
        
        <div class="card-body p-4 d-flex flex-column">
          <div class="d-flex align-items-center gap-3 text-muted small mb-2">
            <span><i class="bi bi-calendar3 me-1 text-primary"></i> {{ $tanggal }}</span>
            <span><i class="bi bi-person-circle me-1 text-primary"></i> {{ $b->penulis ?? 'Admin Soreang' }}</span>
          </div>

          <h5 class="card-title fw-bold text-dark fs-6 mb-2 line-clamp-2" style="line-height: 1.4;">
            <a href="{{ route('berita.public.show', $b->slug ?? $b->id) }}" class="text-dark text-decoration-none hover-primary">
              {{ $b->judul }}
            </a>
          </h5>

          <p class="card-text text-muted small mb-4 flex-grow-1 line-clamp-3" style="line-height: 1.6;">
            {{ Str::limit(strip_tags($b->ringkasan ?? $b->isi ?? ''), 120) }}
          </p>

          <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
            <a href="{{ route('berita.public.show', $b->slug ?? $b->id) }}" class="fw-semibold text-primary text-decoration-none small d-flex align-items-center">
              Baca Artikel <i class="bi bi-arrow-right ms-1"></i>
            </a>
            <small class="text-muted"><i class="bi bi-clock me-1"></i> Informasi Publik</small>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-5">
      <div class="p-5 bg-white rounded-4 shadow-sm max-w-md mx-auto">
        <i class="bi bi-newspaper display-1 text-muted opacity-50 d-block mb-3"></i>
        <h4 class="fw-bold text-dark mb-2">Tidak Ada Berita Ditemukan</h4>
        <p class="text-muted small mb-4">Maaf, berita atau pengumuman yang Anda cari tidak ditemukan.</p>
        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="s3ResetBeritaFilter()">Lihat Semua Berita</button>
      </div>
    </div>
  @endforelse
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-center mt-5" id="beritaPaginationWrapper">
  {{ $beritaList->links() }}
</div>
