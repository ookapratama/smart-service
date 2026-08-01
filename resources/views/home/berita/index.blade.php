@extends('home.layouts.app')

@section('title', 'Portal Berita & Publikasi - ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('meta_description', 'Pemberitaan resmi, kabar pembangunan, dan pengumuman publik Kecamatan Soreang Kota Parepare.')

@push('styles')
<style>
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
  #berita-grid-container.loading {
    opacity: 0.5;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }
</style>
@endpush

@section('content')

  <!-- 1. HERO BANNER PORTAL BERITA -->
  <section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.88) 0%, rgba(4, 18, 55, 0.92) 100%), url('{{ !empty($siteInfo['hero_bg']) ? asset('storage/' . $siteInfo['hero_bg']) : asset('assets/home/img/soreang-hero.png') }}') center/cover no-repeat !important; min-height: 45vh; display: flex; align-items: center;">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center text-center">
        <div class="col-lg-8" data-aos="fade-up">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm mx-auto">
            <i class="bi bi-newspaper text-dark"></i>
            <span class="small fw-semibold text-dark">Portal Berita & Publikasi Resmi</span>
          </div>

          <h1 class="display-5 fw-extrabold mb-3 text-white">Kabar Kecamatan Soreang</h1>
          
          <p class="fs-5 mb-0" style="color: #f1f5f9;">
            Informasi terkini seputar kegiatan pemerintahan, pembangunan wilayah, sosialisasi program, dan pelayanan publik di {{ $siteInfo['kecamatan'] ?? 'Kecamatan Soreang' }}.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. MAIN CONTENT BERITA KATALOG -->
  <section class="py-5 bg-light">
    <div class="container">
      
      <!-- Filter Bar & Search (Matching Agenda & Galeri) -->
      <div class="row g-3 justify-content-between align-items-center mb-4">
        <div class="col-lg-8">
          <div class="d-flex flex-wrap gap-2" id="beritaCategoryPills">
            <button type="button" data-kategori="" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold berita-filter-btn {{ !request('kategori') ? 'btn-primary active' : 'btn-white border text-dark' }}">
              Semua Kategori
            </button>
            @foreach($categories as $cat)
              <button type="button" data-kategori="{{ $cat }}" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold berita-filter-btn {{ request('kategori') === $cat ? 'btn-primary active' : 'btn-white border text-dark' }}">
                {{ $cat }}
              </button>
            @endforeach
          </div>
        </div>

        <div class="col-lg-4">
          <form id="beritaSearchForm" action="{{ route('berita.public.index') }}" method="GET" onsubmit="return false;">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
              <input type="text" id="beritaSearchInput" name="q" class="form-control border-0 px-4 fs-6" placeholder="Cari berita / pengumuman..." value="{{ request('q') }}">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- News Cards Grid Container -->
      <div id="berita-grid-container" class="position-relative">
        @include('home.berita.partials.grid', ['beritaList' => $beritaList])
      </div>

    </div>
  </section>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const beritaContainer = document.getElementById('berita-grid-container');
    const searchForm = document.getElementById('beritaSearchForm');
    const searchInput = document.getElementById('beritaSearchInput');
    const filterButtons = document.querySelectorAll('.berita-filter-btn');

    let currentCategory = "{{ request('kategori') }}";
    let searchKeyword = "{{ request('q') }}";
    let debounceTimer = null;

    function fetchBerita(page = 1) {
      if (beritaContainer) {
        beritaContainer.classList.add('loading');
      }

      const params = new URLSearchParams();
      if (currentCategory) params.append('kategori', currentCategory);
      if (searchKeyword) params.append('q', searchKeyword);
      if (page > 1) params.append('page', page);
      params.append('ajax', '1');

      const fetchUrl = "{{ route('berita.public.index') }}?" + params.toString();

      fetch(fetchUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (beritaContainer) {
          beritaContainer.classList.remove('loading');
          beritaContainer.innerHTML = data.html;
        }

        // Update URL state without page refresh
        const pushParams = new URLSearchParams();
        if (currentCategory) pushParams.append('kategori', currentCategory);
        if (searchKeyword) pushParams.append('q', searchKeyword);
        if (page > 1) pushParams.append('page', page);

        const newUrl = window.location.pathname + (pushParams.toString() ? '?' + pushParams.toString() : '');
        history.pushState(null, '', newUrl);

        bindPaginationEvents();
      })
      .catch(err => {
        console.error('Error fetching berita:', err);
        if (beritaContainer) beritaContainer.classList.remove('loading');
      });
    }

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
            document.getElementById('beritaCategoryPills')?.scrollIntoView({ behavior: 'smooth' });
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
        fetchBerita(1);
      });
    });

    // Realtime search with 300ms debounce
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        searchKeyword = this.value.trim();
        debounceTimer = setTimeout(() => {
          fetchBerita(1);
        }, 300);
      });
    }

    if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        searchKeyword = searchInput ? searchInput.value.trim() : '';
        fetchBerita(1);
      });
    }

    bindPaginationEvents();
  });
</script>
@endpush
