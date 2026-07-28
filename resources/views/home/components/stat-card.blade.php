@props([
    'count' => 0,
    'suffix' => '',
    'label' => '',
    'description' => '',
    'icon' => 'bi-bar-chart-fill',
    'delay' => 100
])

<div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $delay }}">
  <div class="stats-item d-flex align-items-center w-100 h-100 p-4 bg-white rounded-4 shadow-sm border">
    <div class="icon-wrap me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary" style="width: 54px; height: 54px; font-size: 1.5rem;">
      <i class="bi {{ $icon }}"></i>
    </div>
    <div>
      <div class="d-flex align-items-baseline">
        <span data-purecounter-start="0" data-purecounter-end="{{ (float)$count }}" data-purecounter-duration="1" class="purecounter fw-bold fs-3 text-dark"></span>
        <span class="fw-bold fs-4 text-primary ms-1">{{ $suffix }}</span>
      </div>
      <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ $label }}</h6>
      @if($description)
        <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $description }}</small>
      @endif
    </div>
  </div>
</div>
