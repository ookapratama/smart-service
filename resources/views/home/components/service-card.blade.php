<div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $delay ?? 100 }}">
  <div class="service-item item-cyan position-relative">
    <div class="icon">
      <i class="bi {{ $icon ?? 'bi-activity' }}"></i>
    </div>
    <a href="{{ $link ?? '#' }}" class="stretched-link">
      <h3>{{ $title ?? 'Service Title' }}</h3>
    </a>
    <p>{{ $description ?? '' }}</p>
  </div>
</div>
