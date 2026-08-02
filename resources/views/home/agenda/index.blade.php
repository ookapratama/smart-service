@extends('home.layouts.app')

@section('title', 'Agenda & Kalender Kegiatan - ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('meta_description', 'Jadwal dan kalender agenda kegiatan resmi, musrenbang, sosialisasi, dan pelayanan keliling di Kecamatan Soreang Kota Parepare.')

@push('styles')
<style>
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  #agenda-grid-container.loading {
    opacity: 0.5;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }
</style>
@endpush

@section('content')

  <!-- 1. HERO BANNER AGENDA -->
  <section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.88) 0%, rgba(4, 18, 55, 0.92) 100%), url('{{ !empty($siteInfo['hero_bg']) ? asset('storage/' . $siteInfo['hero_bg']) : asset('assets/home/img/soreang-hero.png') }}') center/cover no-repeat !important; min-height: 45vh; display: flex; align-items: center;">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center text-center">
        <div class="col-lg-8" data-aos="fade-up">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm mx-auto">
            <i class="bi bi-calendar-event text-dark"></i>
            <span class="small fw-semibold text-dark">Kalender & Rencana Kegiatan</span>
          </div>
          <h1 class="display-5 fw-extrabold mb-3 text-white">Agenda Kegiatan Wilayah</h1>
          <p class="fs-5 mb-0" style="color: #f1f5f9;">
            Pantau jadwal rapat koordinasi, musrenbang, sosialisasi, dan pelayanan keliling {{ $siteInfo['kecamatan'] ?? 'Kecamatan Soreang' }}.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. HIGHLIGHT UPCOMING AGENDA -->
  @if($upcomingAgenda)
    <section class="py-4 bg-white border-bottom">
      <div class="container">
        <div class="p-4 bg-primary bg-gradient text-white rounded-4 shadow-sm position-relative overflow-hidden">
          <div class="row align-items-center gy-3">
            <div class="col-lg-8">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1"><i class="bi bi-star-fill me-1"></i> Agenda Utama Mendatang</span>
                <span class="badge bg-white text-dark bg-opacity-20 rounded-pill px-3 py-1 fs-7">{{ $upcomingAgenda->kategori }}</span>
              </div>
              <h4 class="fw-bold text-white mb-2">{{ $upcomingAgenda->judul }}</h4>
              <p class="text-white-50 small mb-0"><i class="bi bi-geo-alt me-1"></i> {{ $upcomingAgenda->lokasi ?? 'Kecamatan Soreang' }} • <i class="bi bi-clock me-1 ms-2"></i> {{ optional($upcomingAgenda->mulai_at)->format('d M Y H:i') }} WITA</p>
            </div>
            <div class="col-lg-4 text-lg-end">
              <a href="{{ route('agenda.public.show', $upcomingAgenda->slug ?: $upcomingAgenda->id) }}" class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold hover-lift">
                Lihat Detail Agenda <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif

  <!-- 3. FILTER & CATALOG AGENDA -->
  <section class="py-5 bg-light">
    <div class="container">
      
      <!-- Filter Bar -->
      <div class="row g-3 justify-content-between align-items-center mb-4">
        <div class="col-lg-8">
          <div class="d-flex flex-wrap gap-2" id="agendaCategoryPills">
            <button type="button" data-kategori="" data-status="" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold agenda-filter-btn {{ !request('status') && !request('kategori') ? 'btn-primary active' : 'btn-white border text-dark' }}">
              Semua Agenda
            </button>
            <button type="button" data-kategori="" data-status="mendatang" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold agenda-filter-btn {{ request('status') == 'mendatang' ? 'btn-primary active' : 'btn-white border text-dark' }}">
              <i class="bi bi-clock-history me-1"></i> Mendatang
            </button>
            <button type="button" data-kategori="" data-status="selesai" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold agenda-filter-btn {{ request('status') == 'selesai' ? 'btn-primary active' : 'btn-white border text-dark' }}">
              <i class="bi bi-check-circle me-1"></i> Selesai
            </button>
            @foreach($categories as $cat)
              <button type="button" data-kategori="{{ $cat }}" data-status="" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold agenda-filter-btn {{ request('kategori') == $cat ? 'btn-primary active' : 'btn-white border text-dark' }}">
                {{ $cat }}
              </button>
            @endforeach
          </div>
        </div>

        <div class="col-lg-4">
          <form id="agendaSearchForm" action="{{ route('agenda.public.index') }}" method="GET" onsubmit="return false;">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
              <input type="text" id="agendaSearchInput" name="q" class="form-control border-0 px-4 fs-6" placeholder="Cari judul / lokasi agenda..." value="{{ request('q') }}">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Grid Catalog -->
      <div id="agenda-grid-container" class="position-relative">
        @include('home.agenda.partials.grid', ['agendaList' => $agendaList])
      </div>

    </div>
  </section>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const agendaContainer = document.getElementById('agenda-grid-container');
    const searchForm = document.getElementById('agendaSearchForm');
    const searchInput = document.getElementById('agendaSearchInput');
    const filterButtons = document.querySelectorAll('.agenda-filter-btn');

    let currentCategory = "{{ request('kategori') }}";
    let currentStatus = "{{ request('status') }}";
    let searchKeyword = "{{ request('q') }}";
    let debounceTimer = null;

    function fetchAgenda(page = 1) {
      if (agendaContainer) {
        agendaContainer.classList.add('loading');
      }

      const params = new URLSearchParams();
      if (currentCategory) params.append('kategori', currentCategory);
      if (currentStatus) params.append('status', currentStatus);
      if (searchKeyword) params.append('q', searchKeyword);
      if (page > 1) params.append('page', page);
      params.append('ajax', '1');

      const fetchUrl = "{{ route('agenda.public.index') }}?" + params.toString();

      fetch(fetchUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (agendaContainer) {
          agendaContainer.classList.remove('loading');
          agendaContainer.innerHTML = data.html;
        }

        // Update URL state without page refresh
        const pushParams = new URLSearchParams();
        if (currentCategory) pushParams.append('kategori', currentCategory);
        if (currentStatus) pushParams.append('status', currentStatus);
        if (searchKeyword) pushParams.append('q', searchKeyword);
        if (page > 1) pushParams.append('page', page);

        const newUrl = window.location.pathname + (pushParams.toString() ? '?' + pushParams.toString() : '');
        history.pushState(null, '', newUrl);

        bindPaginationEvents();
      })
      .catch(err => {
        console.error('Error fetching agenda:', err);
        if (agendaContainer) agendaContainer.classList.remove('loading');
      });
    }

    function bindPaginationEvents() {
      if (!agendaContainer) return;
      const paginationLinks = agendaContainer.querySelectorAll('.pagination a');
      paginationLinks.forEach(link => {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const href = this.getAttribute('href');
          if (href) {
            const urlObj = new URL(href, window.location.origin);
            const pageParam = urlObj.searchParams.get('page') || 1;
            fetchAgenda(pageParam);
            document.getElementById('agendaCategoryPills')?.scrollIntoView({ behavior: 'smooth' });
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
        currentStatus = this.dataset.status || '';
        fetchAgenda(1);
      });
    });

    // Realtime search with 300ms debounce
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        searchKeyword = this.value.trim();
        debounceTimer = setTimeout(() => {
          fetchAgenda(1);
        }, 300);
      });
    }

    if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        searchKeyword = searchInput ? searchInput.value.trim() : '';
        fetchAgenda(1);
      });
    }

    bindPaginationEvents();
  });
</script>
@endpush
