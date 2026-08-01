@extends('home.layouts.app')

@section('title', 'Galeri Foto & Video Kegiatan - ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('meta_description', 'Dokumentasi foto dan video kegiatan publik, pelayanan kelurahan, sosialisasi, dan aktivitas masyarakat Kecamatan Soreang.')

@push('styles')
<style>
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .hover-scale {
    transition: transform 0.25s ease;
  }
  .hover-scale:hover {
    transform: translate(-50%, -50%) scale(1.12) !important;
  }
  #galeri-grid-container.loading {
    opacity: 0.5;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }
</style>
@endpush

@section('content')

  <!-- 1. HERO BANNER GALERI -->
  <section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.88) 0%, rgba(4, 18, 55, 0.92) 100%), url('{{ !empty($siteInfo['hero_bg']) ? asset('storage/' . $siteInfo['hero_bg']) : asset('assets/home/img/soreang-hero.png') }}') center/cover no-repeat !important; min-height: 45vh; display: flex; align-items: center;">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center text-center">
        <div class="col-lg-8" data-aos="fade-up">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm mx-auto">
            <i class="bi bi-images text-white"></i>
            <span class="small fw-semibold text-white">Dokumentasi & Album Publik</span>
          </div>
          <h1 class="display-5 fw-extrabold mb-3 text-white">Galeri Foto & Video</h1>
          <p class="fs-5 mb-0" style="color: #f1f5f9;">
            Dokumentasi liputan kegiatan resmi, inovasi pelayanan publik, dan momen kemasyarakatan {{ $siteInfo['kecamatan'] ?? 'Kecamatan Soreang' }}.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. FILTER & CATALOG GALERI -->
  <section class="py-5 bg-light">
    <div class="container">
      
      <!-- Filter Bar -->
      <div class="row g-3 justify-content-between align-items-center mb-4">
        <div class="col-lg-8">
          <div class="d-flex flex-wrap gap-2" id="galeriCategoryPills">
            <button type="button" data-kategori="" data-tipe="" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold galeri-filter-btn {{ !request('kategori') && !request('tipe') ? 'btn-primary active' : 'btn-white border text-dark' }}">
              Semua Media
            </button>
            <button type="button" data-kategori="" data-tipe="foto" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold galeri-filter-btn {{ request('tipe') == 'foto' ? 'btn-primary active' : 'btn-white border text-dark' }}">
              <i class="bi bi-image me-1"></i> Foto
            </button>
            <button type="button" data-kategori="" data-tipe="video" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold galeri-filter-btn {{ request('tipe') == 'video' ? 'btn-primary active' : 'btn-white border text-dark' }}">
              <i class="bi bi-play-circle me-1"></i> Video
            </button>
            @foreach($categories as $cat)
              <button type="button" data-kategori="{{ $cat }}" data-tipe="" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold galeri-filter-btn {{ request('kategori') == $cat ? 'btn-primary active' : 'btn-white border text-dark' }}">
                {{ $cat }}
              </button>
            @endforeach
          </div>
        </div>

        <div class="col-lg-4">
          <form id="galeriSearchForm" action="{{ route('galeri.public.index') }}" method="GET" onsubmit="return false;">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
              <input type="text" id="galeriSearchInput" name="q" class="form-control border-0 px-4 fs-6" placeholder="Cari judul / kegiatan..." value="{{ request('q') }}">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Grid Catalog -->
      <div id="galeri-grid-container" class="position-relative">
        @include('home.galeri.partials.grid', ['galeriList' => $galeriList])
      </div>

    </div>
  </section>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const galeriContainer = document.getElementById('galeri-grid-container');
    const searchForm = document.getElementById('galeriSearchForm');
    const searchInput = document.getElementById('galeriSearchInput');
    const filterButtons = document.querySelectorAll('.galeri-filter-btn');

    let currentCategory = "{{ request('kategori') }}";
    let currentTipe = "{{ request('tipe') }}";
    let searchKeyword = "{{ request('q') }}";
    let debounceTimer = null;

    function fetchGaleri(page = 1) {
      if (galeriContainer) {
        galeriContainer.classList.add('loading');
      }

      const params = new URLSearchParams();
      if (currentCategory) params.append('kategori', currentCategory);
      if (currentTipe) params.append('tipe', currentTipe);
      if (searchKeyword) params.append('q', searchKeyword);
      if (page > 1) params.append('page', page);
      params.append('ajax', '1');

      const fetchUrl = "{{ route('galeri.public.index') }}?" + params.toString();

      fetch(fetchUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (galeriContainer) {
          galeriContainer.classList.remove('loading');
          galeriContainer.innerHTML = data.html;
        }

        // Update URL state without page refresh
        const pushParams = new URLSearchParams();
        if (currentCategory) pushParams.append('kategori', currentCategory);
        if (currentTipe) pushParams.append('tipe', currentTipe);
        if (searchKeyword) pushParams.append('q', searchKeyword);
        if (page > 1) pushParams.append('page', page);

        const newUrl = window.location.pathname + (pushParams.toString() ? '?' + pushParams.toString() : '');
        history.pushState(null, '', newUrl);

        // Re-initialize GLightbox if available
        if (typeof GLightbox !== 'undefined') {
          GLightbox({ selector: '.glightbox' });
        }

        bindPaginationEvents();
      })
      .catch(err => {
        console.error('Error fetching galeri:', err);
        if (galeriContainer) galeriContainer.classList.remove('loading');
      });
    }

    function bindPaginationEvents() {
      if (!galeriContainer) return;
      const paginationLinks = galeriContainer.querySelectorAll('.pagination a');
      paginationLinks.forEach(link => {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const href = this.getAttribute('href');
          if (href) {
            const urlObj = new URL(href, window.location.origin);
            const pageParam = urlObj.searchParams.get('page') || 1;
            fetchGaleri(pageParam);
            document.getElementById('galeriCategoryPills')?.scrollIntoView({ behavior: 'smooth' });
          }
        });
      });
    }

    // Filter button clicks
    filterButtons.forEach(btn => {
      btn.addEventListener('click', function () {
        filterButtons.forEach(b => {
          b.classList.remove('btn-primary', 'active');
          b.classList.add('btn-white', 'border', 'text-dark');
        });
        this.classList.remove('btn-white', 'border', 'text-dark');
        this.classList.add('btn-primary', 'active');

        currentCategory = this.dataset.kategori || '';
        currentTipe = this.dataset.tipe || '';
        fetchGaleri(1);
      });
    });

    // Realtime search with 300ms debounce
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        searchKeyword = this.value.trim();
        debounceTimer = setTimeout(() => {
          fetchGaleri(1);
        }, 300);
      });
    }

    if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        searchKeyword = searchInput ? searchInput.value.trim() : '';
        fetchGaleri(1);
      });
    }

    bindPaginationEvents();
  });
</script>
@endpush
