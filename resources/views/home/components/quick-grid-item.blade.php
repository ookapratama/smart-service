@props([
    'title' => 'Menu',
    'icon' => 'bi-grid',
    'badge' => null,
    'link' => '#',
    'isModal' => false,
    'modalTarget' => '',
    'delay' => 100,
    'color' => 'primary'
])

<div class="col-lg-3 col-md-4 col-6" data-aos="fade-up" data-aos-delay="{{ $delay }}">
  @if($isModal)
    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="{{ $modalTarget }}" class="text-decoration-none">
  @else
    <a href="{{ $link }}" class="text-decoration-none">
  @endif
    <div class="quick-grid-item text-center p-3 p-lg-4 bg-white rounded-4 shadow-sm border h-100 position-relative transition-all hover-lift">
      @if($badge)
        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-{{ $color }} shadow-sm text-white" style="font-size: 0.7rem;">
          {{ $badge }}
        </span>
      @endif
      <div class="quick-grid-icon mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-{{ $color }}-subtle text-{{ $color }}" style="width: 58px; height: 58px; font-size: 1.5rem;">
        <i class="bi {{ $icon }}"></i>
      </div>
      <h6 class="fw-bold text-dark mb-1 fs-6">{{ $title }}</h6>
      @if(isset($description))
        <p class="text-muted small mb-0 d-none d-md-block" style="font-size: 0.78rem;">{{ $description }}</p>
      @endif
    </div>
  </a>
</div>
