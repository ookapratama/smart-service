@extends('home.layouts.app')

@section('title', 'Tiket Saya - ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('body-class', 'starter-page-page')

@push('styles')
<style>
  #tiket-grid-container.loading {
    opacity: 0.5;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }
</style>
@endpush

@section('content')
<section class="section py-5" style="min-height: 60vh;">
  <div class="container" data-aos="fade-up">

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4 mt-3">
      <div>
        <h2 class="fw-bold mb-1">Tiket Saya</h2>
        <p class="text-muted mb-0">Riwayat pengajuan surat dan pengaduan atas nama <strong>{{ $pemohon->name }}</strong>.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('surat.index') }}" class="btn btn-sm btn-primary rounded-pill px-3">
          <i class="bi bi-file-earmark-plus me-1"></i> Ajukan Surat
        </a>
        <a href="{{ route('pengaduan.create') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
          <i class="bi bi-megaphone me-1"></i> Buat Pengaduan
        </a>
      </div>
    </div>

    <!-- Filter Layanan & Pencarian (jQuery Driven) -->
    <div class="row g-3 justify-content-between align-items-center mb-4 border-bottom pb-3">
      <div class="col-lg-7">
        <div class="d-flex flex-wrap gap-2" id="tiketTypePills">
          <button type="button" data-type="all" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold tiket-filter-btn {{ request('type', 'all') === 'all' ? 'btn-primary active' : 'btn-white border text-dark' }}">
            <i class="bi bi-grid-fill me-1"></i> Semua Tiket
          </button>
          <button type="button" data-type="pengajuan_surat" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold tiket-filter-btn {{ request('type') === 'pengajuan_surat' ? 'btn-primary active' : 'btn-white border text-dark' }}">
            <i class="bi bi-file-earmark-text me-1"></i> Pengajuan Surat
          </button>
          <button type="button" data-type="pengaduan" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold tiket-filter-btn {{ request('type') === 'pengaduan' ? 'btn-primary active' : 'btn-white border text-dark' }}">
            <i class="bi bi-megaphone me-1"></i> Pengaduan
          </button>
        </div>
      </div>

      <div class="col-lg-5">
        <form id="tiketSearchForm" action="{{ route('tiket-saya.index') }}" method="GET" onsubmit="return false;">
          <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
            <input type="text" id="tiketSearchInput" name="q" class="form-control border-0 px-4 fs-6" placeholder="Cari nomor tiket / permohonan..." value="{{ request('q') }}">
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Grid Tiket Container -->
    <div id="tiket-grid-container" class="position-relative">
      @include('home.tiket-saya.partials.grid', ['tikets' => $tikets])
    </div>

  </div>
</section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $(document).ready(function () {
    const $container = $('#tiket-grid-container');
    const $searchInput = $('#tiketSearchInput');
    const $searchForm = $('#tiketSearchForm');
    const $filterButtons = $('.tiket-filter-btn');

    let currentType = "{{ request('type', 'all') }}";
    let searchKeyword = "{{ request('q') }}";
    let debounceTimer = null;

    function fetchTiket(page) {
      page = page || 1;
      $container.addClass('loading');

      const params = {
        ajax: 1,
        type: currentType,
        q: searchKeyword,
        page: page
      };

      $.ajax({
        url: "{{ route('tiket-saya.index') }}",
        type: "GET",
        data: params,
        dataType: "json",
        success: function (data) {
          $container.removeClass('loading');
          $container.html(data.html);

          // Update URL browser state tanpa reload halaman
          const pushParams = new URLSearchParams();
          if (currentType && currentType !== 'all') pushParams.append('type', currentType);
          if (searchKeyword) pushParams.append('q', searchKeyword);
          if (page > 1) pushParams.append('page', page);

          const newUrl = window.location.pathname + (pushParams.toString() ? '?' + pushParams.toString() : '');
          history.pushState(null, '', newUrl);

          bindPagination();
        },
        error: function (err) {
          console.error('Gagal mengambil data tiket:', err);
          $container.removeClass('loading');
        }
      });
    }

    function bindPagination() {
      $container.find('.pagination a').off('click').on('click', function (e) {
        e.preventDefault();
        const href = $(this).attr('href');
        if (href) {
          const urlObj = new URL(href, window.location.origin);
          const pageParam = urlObj.searchParams.get('page') || 1;
          fetchTiket(pageParam);
          $('html, body').animate({
            scrollTop: $('#tiketTypePills').offset().top - 100
          }, 300);
        }
      });
    }

    // Event Filter Tombol Pills
    $filterButtons.on('click', function () {
      $filterButtons.removeClass('btn-primary active').addClass('btn-white border text-dark');
      $(this).removeClass('btn-white border text-dark').addClass('btn-primary active');

      currentType = $(this).data('type') || 'all';
      fetchTiket(1);
    });

    // Realtime search dengan 300ms debounce
    $searchInput.on('input', function () {
      clearTimeout(debounceTimer);
      searchKeyword = $.trim($(this).val());
      debounceTimer = setTimeout(function () {
        fetchTiket(1);
      }, 300);
    });

    $searchForm.on('submit', function (e) {
      e.preventDefault();
      clearTimeout(debounceTimer);
      searchKeyword = $.trim($searchInput.val());
      fetchTiket(1);
    });

    bindPagination();
  });
</script>
@endpush
