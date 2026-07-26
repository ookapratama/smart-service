<div class="container section-title" data-aos="fade-up">
  <h2>{{ $subtitle ?? 'Section Subtitle' }}</h2>
  <p><span>{{ $title ?? 'Section Title' }}</span></p>
  @if(!empty($description))
    <div><span>{{ $description }}</span></div>
  @endif
</div>
