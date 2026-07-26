<div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="{{ $delay ?? 100 }}">
  <div class="service-item position-relative">
    <div class="icon"><i class="bi {{ $icon ?? 'bi-activity' }} icon"></i></div>
    <h4><a href="{{ $link ?? '#' }}" class="stretched-link">{{ $title ?? 'Service Title' }}</a></h4>
    <p>{{ $description ?? '' }}</p>
  </div>
</div>
