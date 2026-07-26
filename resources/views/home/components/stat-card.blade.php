<div class="col-lg-3 col-md-6">
  <div class="stats-item d-flex align-items-center w-100 h-100">
    <i class="bi {{ $icon ?? 'bi-emoji-smile' }} color-blue flex-shrink-0"></i>
    <div>
      <span data-purecounter-start="0" data-purecounter-end="{{ $count ?? 0 }}" data-purecounter-duration="{{ $duration ?? 1 }}" class="purecounter"></span>
      <p>{{ $label ?? '' }}</p>
    </div>
  </div>
</div>
