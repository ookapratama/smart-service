<div class="row gy-4">
    @forelse($agendaList as $index => $item)
        @php
            $isPast = optional($item->mulai_at)->isPast();
            $imgPath = $item->gambar;
            if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
            }
        @endphp
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift transition-all bg-white d-flex flex-column">
                <div class="position-relative overflow-hidden bg-primary bg-gradient p-3 text-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold fs-7">{{ $item->kategori }}</span>
                        @if($isPast)
                            <span class="badge bg-success rounded-pill px-3 py-1 fs-7">Selesai</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fs-7"><i class="bi bi-clock me-1"></i> Mendatang</span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <div class="bg-white text-primary rounded-4 text-center p-2 flex-shrink-0 shadow-sm" style="min-width: 60px;">
                            <span class="d-block fs-4 fw-extrabold m-0 leading-none">{{ optional($item->mulai_at)->format('d') }}</span>
                            <small class="text-uppercase fw-bold font-monospace" style="font-size: 0.7rem;">{{ optional($item->mulai_at)->format('M Y') }}</small>
                        </div>
                        <div>
                            <small class="text-white-50 font-monospace d-block" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> {{ optional($item->mulai_at)->format('H:i') }} WITA</small>
                            <span class="fw-semibold small text-white text-truncate d-block" style="max-width: 200px;"><i class="bi bi-geo-alt me-1"></i> {{ $item->lokasi ?? 'Kecamatan Soreang' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                    <div>
                        <h5 class="card-title fw-bold text-dark fs-6 mb-2 leading-snug">
                            <a href="{{ route('agenda.public.show', $item->slug ?: $item->id) }}" class="text-dark text-decoration-none hover-primary">
                                {{ $item->judul }}
                            </a>
                        </h5>
                        <p class="card-text text-secondary small line-clamp-2 mb-0" style="line-height: 1.6;">
                            {{ $item->ringkasan ?: str($item->deskripsi)->limit(90) }}
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between">
                        <small class="text-muted"><i class="bi bi-building me-1"></i> {{ $item->penyelenggara ?? 'Kecamatan Soreang' }}</small>
                        <a href="{{ route('agenda.public.show', $item->slug ?: $item->id) }}" class="fw-semibold text-primary small text-decoration-none d-inline-flex align-items-center gap-1 hover-gap">
                            Detail Agenda <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-4 border shadow-sm max-w-md mx-auto">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Tidak Ada Agenda Ditemukan</h5>
                <p class="text-muted small mb-0">Belum ada agenda kegiatan yang terdaftar untuk kriteria filter yang Anda pilih.</p>
            </div>
        </div>
    @endforelse
</div>

@if($agendaList->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $agendaList->links('pagination::bootstrap-5') }}
    </div>
@endif
