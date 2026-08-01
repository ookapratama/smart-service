@extends('home.layouts.app')

@section('title', 'Portal Berita & Agenda - Kecamatan Soreang Kota Parepare')
@section('meta_description', 'Pemberitaan resmi, kabar pembangunan, dan agenda publik Kecamatan Soreang Kota Parepare.')

@push('styles')
<style>
  .hover-lift {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08) !important;
  }
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .category-pill.active {
    background-color: var(--bs-primary) !important;
    color: #ffffff !important;
    border-color: var(--bs-primary) !important;
  }
  #beritaContainer.loading {
    opacity: 0.5;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }
</style>
@endpush

@section('content')

  <!-- 1. HERO BANNER PORTAL BERITA (Rich Dark Blue Overlay with Background Photo & High Contrast) -->
  <section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.88) 0%, rgba(4, 18, 55, 0.92) 100%), url('{{ !empty($siteInfo['hero_bg']) ? asset('storage/' . $siteInfo['hero_bg']) : asset('assets/home/img/soreang-hero.png') }}') center/cover no-repeat !important; min-height: 50vh; display: flex; align-items: center;">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center text-center">
        <div class="col-lg-8" data-aos="fade-up">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm mx-auto">
            <i class="bi bi-newspaper text-dark"></i>
            <span class="small fw-semibold text-dark">Portal Berita & Publikasi Kecamatan Soreang</span>
          </div>

          <h1 class="display-5 fw-extrabold mb-3" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">Kabar Kecamatan Soreang</h1>
          
          <p class="fs-5 mb-4" style="color: #f1f5f9 !important; line-height: 1.6; font-weight: 400; text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);">
            Informasi terkini seputar kegiatan pemerintahan, pembangunan wilayah, sosialisasi program, dan pelayanan publik di Kecamatan Soreang Kota Parepare.
          </p>
          
          <!-- Search Bar Form (AJAX Dynamic) -->
          <form id="s3BeritaSearchForm" class="max-w-xl mx-auto shadow-lg rounded-pill overflow-hidden bg-white p-1">
            <div class="input-group">
              <span class="input-group-text bg-white border-0 ps-3 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" id="s3BeritaSearchInput" value="{{ request('q') }}" class="form-control border-0 fs-6 px-2 text-dark" placeholder="Cari berita atau pengumuman..." autocomplete="off">
              <button type="submit" id="s3BeritaSearchBtn" class="btn btn-primary rounded-pill px-4 fw-bold text-white">
                <span>Cari Berita</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. MAIN CONTENT BERITA KATALOG -->
  <section class="py-5 bg-light">
    <div class="container">
      
      <!-- Category Filter Pills (AJAX Dynamic) -->
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2 overflow-auto py-1" id="s3CategoryPillGroup">
          <button type="button" data-kategori="" class="category-pill btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ !request('kategori') ? 'btn-primary active' : 'btn-outline-secondary bg-white' }}">
            Semua Kategori
          </button>
          @foreach($categories as $cat)
            <button type="button" data-kategori="{{ $cat }}" class="category-pill btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ request('kategori') === $cat ? 'btn-primary active' : 'btn-outline-secondary bg-white' }}">
              {{ $cat }}
            </button>
          @endforeach
        </div>

        <div class="d-flex align-items-center gap-2">
          <div id="s3BeritaLoader" class="spinner-border spinner-border-sm text-primary d-none" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <span class="text-muted small">
            Menampilkan <strong id="s3BeritaTotalCount">{{ $beritaList->total() }}</strong> publikasi
          </span>
        </div>
      </div>

      <!-- News Cards & Pagination Container -->
      <div id="beritaContainer">
        @include('home.berita.partials.grid')
      </div>

    </div>
  </section>

@endsection

@push('scripts')
<script>
  (function () {
    'use strict';

    let currentCategory = "{{ request('kategori', '') }}";
    let searchKeyword = "{{ request('q', '') }}";
    let currentPage = 1;
    let debounceTimer = null;

    const beritaContainer = document.getElementById('beritaContainer');
    const searchInput = document.getElementById('s3BeritaSearchInput');
    const searchForm = document.getElementById('s3BeritaSearchForm');
    const loader = document.getElementById('s3BeritaLoader');
    const totalCount = document.getElementById('s3BeritaTotalCount');
    const categoryButtons = document.querySelectorAll('#s3CategoryPillGroup .category-pill');

    // Function to fetch news via AJAX
    function fetchBerita(page = 1) {
      currentPage = page;
      if (loader) loader.classList.remove('d-none');
      if (beritaContainer) beritaContainer.classList.add('loading');

      const params = new URLSearchParams();
      params.append('ajax', '1');
      if (page > 1) params.append('page', page);
      if (currentCategory) params.append('kategori', currentCategory);
      if (searchKeyword) params.append('q', searchKeyword);

      const fetchUrl = "{{ route('berita.public.index') }}?" + params.toString();

      fetch(fetchUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (loader) loader.classList.add('d-none');
        if (beritaContainer) {
          beritaContainer.classList.remove('loading');
          beritaContainer.innerHTML = data.html;
        }
        if (totalCount && data.total !== undefined) {
          totalCount.textContent = data.total;
        }

        // Update browser URL query without reloading
        const pushParams = new URLSearchParams();
        if (currentCategory) pushParams.append('kategori', currentCategory);
        if (searchKeyword) pushParams.append('q', searchKeyword);
        if (page > 1) pushParams.append('page', page);
        
        const newUrl = window.location.pathname + (pushParams.toString() ? '?' + pushParams.toString() : '');
        history.pushState(null, '', newUrl);

        // Re-attach pagination event listeners
        bindPaginationEvents();
      })
      .catch(err => {
        console.error('Error fetching berita:', err);
        if (loader) loader.classList.add('d-none');
        if (beritaContainer) beritaContainer.classList.remove('loading');
      });
    }

    // Intercept pagination clicks dynamically
    function bindPaginationEvents() {
      if (!beritaContainer) return;
      const paginationLinks = beritaContainer.querySelectorAll('.pagination a');
      paginationLinks.forEach(link => {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const href = this.getAttribute('href');
          if (href) {
            const urlObj = new URL(href, window.location.origin);
            const pageParam = urlObj.searchParams.get('page') || 1;
            fetchBerita(pageParam);
            // Smooth scroll up to news container
            document.getElementById('s3CategoryPillGroup')?.scrollIntoView({ behavior: 'smooth' });
          }
        });
      });
    }

    // Bind Category Pill Clicks
    categoryButtons.forEach(btn => {
      btn.addEventListener('click', function () {
        categoryButtons.forEach(b => {
          b.classList.remove('btn-primary', 'active');
          b.classList.add('btn-outline-secondary', 'bg-white');
        });
        this.classList.remove('btn-outline-secondary', 'bg-white');
        this.classList.add('btn-primary', 'active');

        currentCategory = this.dataset.kategori || '';
        fetchBerita(1);
      });
    });

    // Bind Search Input Listener (Debounced)
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        searchKeyword = this.value.trim();
        debounceTimer = setTimeout(() => {
          fetchBerita(1);
        }, 350);
      });
    }

    // Bind Search Form Submit
    if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        searchKeyword = searchInput ? searchInput.value.trim() : '';
        fetchBerita(1);
      });
    }

    // Reset Filter Function
    window.s3ResetBeritaFilter = function () {
      currentCategory = '';
      searchKeyword = '';
      if (searchInput) searchInput.value = '';
      categoryButtons.forEach((b, idx) => {
        if (idx === 0) {
          b.classList.remove('btn-outline-secondary', 'bg-white');
          b.classList.add('btn-primary', 'active');
        } else {
          b.classList.remove('btn-primary', 'active');
          b.classList.add('btn-outline-secondary', 'bg-white');
        }
      });
      fetchBerita(1);
    };

    // Initial binding on page load
    bindPaginationEvents();

  })();
</script>
@endpush
