@props([
    'title',
    'description',
    'icon' => 'bi-gear',
    'badge' => null,
    'link' => '#',
    'linkText' => 'Pelajari Selengkapnya',
    'delay' => 100
])

<div class="col-lg-6 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="{{ $delay }}">
  <div class="service-item position-relative text-start p-4 bg-white rounded-4 shadow-sm border w-100 h-100 hover-lift transition-all">
    <div class="d-flex align-items-center mb-3">
      <div class="icon me-3 m-0 d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 1.4rem;">
        <i class="bi {{ $icon }}"></i>
      </div>
      <div>
        <h3 class="m-0 fs-5 fw-bold text-dark">{{ $title }}</h3>
        @if($badge)
          <span class="badge bg-light text-primary border mt-1">{{ $badge }}</span>
        @endif
      </div>
    </div>
    <p class="text-muted mb-3 flex-grow-1" style="font-size: 0.92rem; line-height: 1.6;">{{ $description }}</p>
    
    @if($link === '#jenis-surat-modal')
      <a href="#jenis-surat-modal" data-bs-toggle="modal" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center">
        {{ $linkText }} <i class="bi bi-arrow-right ms-1"></i>
      </a>
    @elseif($link === '#cek-status')
      <a href="#cek-status" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center">
        {{ $linkText }} <i class="bi bi-arrow-right ms-1"></i>
      </a>
    @else
      <a href="{{ $link }}" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center">
        {{ $linkText }} <i class="bi bi-arrow-right ms-1"></i>
      </a>
    @endif
  </div>
</div>
