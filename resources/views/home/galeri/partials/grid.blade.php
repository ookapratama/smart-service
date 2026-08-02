<div class="row gy-4">
    @forelse($galeriList as $index => $item)
        @php
            $imgPath = $item->gambar;
            if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
            }
            if (!$imgPath) {
                $imgPath = asset('assets/home/img/soreang-hero.png');
            }
        @endphp
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift transition-all bg-white d-flex flex-column">
                <div class="position-relative overflow-hidden bg-dark" style="height: 220px;">
                    <img src="{{ $imgPath }}" class="card-img-top w-100 h-100 object-fit-cover transition-transform duration-500" alt="{{ $item->judul }}">
                    
                    <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                        <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm fs-7">{{ $item->kategori }}</span>
                        @if($item->tipe === 'video')
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm fs-7"><i class="bi bi-play-circle-fill me-1"></i> Video</span>
                        @endif
                    </div>

                    @if($item->tipe === 'video' && $item->video_url)
                        <a href="{{ $item->video_url }}" class="glightbox position-absolute top-50 start-50 translate-middle text-white text-decoration-none bg-primary bg-opacity-75 rounded-circle d-flex align-items-center justify-content-center hover-scale" style="width: 54px; height: 54px; font-size: 1.5rem;">
                            <i class="bi bi-play-fill ms-1"></i>
                        </a>
                    @else
                        <a href="{{ $imgPath }}" class="glightbox position-absolute top-50 start-50 translate-middle text-white text-decoration-none bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center hover-scale" style="width: 48px; height: 48px; font-size: 1.25rem;">
                            <i class="bi bi-arrows-angle-expand"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                    <div>
                        <div class="d-flex align-items-center text-muted small mb-2 gap-3">
                            <span><i class="bi bi-calendar3 me-1 text-primary"></i> {{ optional($item->tgl_kegiatan)->format('d M Y') ?? $item->created_at->format('d M Y') }}</span>
                            <span><i class="bi bi-eye me-1 text-secondary"></i> {{ $item->views ?? 0 }}</span>
                        </div>
                        <h5 class="card-title fw-bold text-dark fs-6 mb-2 leading-snug">
                            <a href="{{ route('galeri.public.show', $item->slug ?: $item->id) }}" class="text-dark text-decoration-none hover-primary">
                                {{ $item->judul }}
                            </a>
                        </h5>
                        @if($item->keterangan)
                            <p class="card-text text-secondary small line-clamp-2 mb-0" style="line-height: 1.6;">
                                {{ str($item->keterangan)->limit(90) }}
                            </p>
                        @endif
                    </div>
                    <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between">
                        <a href="{{ route('galeri.public.show', $item->slug ?: $item->id) }}" class="fw-semibold text-primary small text-decoration-none d-inline-flex align-items-center gap-1 hover-gap">
                            Lihat Detail <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-4 border shadow-sm max-w-md mx-auto">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                    <i class="bi bi-images"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Tidak Ada Galeri Ditemukan</h5>
                <p class="text-muted small mb-0">Belum ada dokumentasi galeri atau foto kegiatan yang cocok dengan kriteria pencarian Anda.</p>
            </div>
        </div>
    @endforelse
</div>

@if($galeriList->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $galeriList->links('pagination::bootstrap-5') }}
    </div>
@endif
