@extends('home.layouts.app')

@section('title', 'Kecamatan Soreang Kota Parepare - Soreang Smart Service (3S)')
@section('meta_description', 'Portal Resmi Pemerintah Kecamatan Soreang Kota Parepare. Informasi Profil Wilayah, Visi Misi, Pelayanan Publik Digital, Peta Wilayah Leaflet, dan Pengaduan Terpadu.')

@push('styles')
<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
  .hover-lift {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08) !important;
  }
  .glass-card {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  .hero-badge {
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }
  .visi-misi-card {
    border-radius: 1.25rem;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
  }
  .visi-misi-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.1);
  }
  #soreangMap {
    height: 490px;
    width: 100%;
    border-radius: 1.25rem;
    z-index: 1;
  }
  #s3TicketResultBox i,
  #s3TicketResultBox span.bi {
    background: none !important;
    width: auto !important;
    height: auto !important;
    border-radius: 0 !important;
    display: inline-block !important;
  }

  /* Override Template CSS White Pseudo-Overlay on Hero */
  #hero::before {
    display: none !important;
  }
</style>
@endpush

@section('content')

  <!-- 1. HERO BANNER SECTION (Rich Dark Blue Overlay with Soreang Background Photo & High Contrast Text) -->
  <section id="hero" class="hero section position-relative py-5 overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.88) 0%, rgba(4, 18, 55, 0.92) 100%), url('{{ !empty($siteInfo['hero_bg']) ? asset('storage/' . $siteInfo['hero_bg']) : asset('assets/home/img/soreang-hero.png') }}') center/cover no-repeat !important; min-height: 80vh; display: flex; align-items: center;">
    
    <div class="container position-relative z-2 text-white py-4">
      <div class="row align-items-center gy-5">
        
        <!-- Left Content -->
        <div class="col-lg-7 order-2 order-lg-1" data-aos="fade-right">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm">
            <span class="small fw-semibold text-dark">{{ $siteInfo['hero_badge'] ?? ('Portal Resmi ' . ($siteInfo['kecamatan'] ?? 'Kecamatan Soreang') . ' • ' . ($siteInfo['kota'] ?? 'Kota Parepare')) }}</span>
          </div>

          <h1 class="display-4 fw-extrabold mb-3" style="color: #ffffff !important; line-height: 1.15; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">
            {{ $siteInfo['hero_title'] ?? ($siteInfo['name'] ?? 'Soreang Smart Service (3S)') }}
          </h1>
          
          <p class="fs-5 mb-4 me-lg-4" style="color: #f1f5f9 !important; line-height: 1.65; font-weight: 400; text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);">
            {{ $siteInfo['hero_subtitle'] ?? ($siteInfo['description'] ?? 'Pelayanan kependudukan digital terpadu, pengaduan publik, dan portal informasi resmi Kecamatan Soreang Kota Parepare secara cepat, mudah, dan transparan.') }}
          </p>

          <div class="d-flex flex-wrap gap-3 align-items-center">
            <a href="#jenis-surat-modal" data-bs-toggle="modal" class="btn btn-light btn-lg rounded-pill px-4 py-3 fw-bold text-primary shadow-lg d-inline-flex align-items-center gap-2 hover-lift">
              <i class="bi bi-file-earmark-plus-fill fs-5"></i>
              <span>{{ $siteInfo['hero_btn1_text'] ?? 'Pengajuan Surat Online' }}</span>
            </a>
            <a href="#profil" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 hover-lift">
              <i class="bi bi-info-circle fs-5"></i>
              <span>{{ $siteInfo['hero_btn2_text'] ?? 'Profil Kecamatan' }}</span>
            </a>
          </div>
        </div>

        <!-- Right Graphic Photo of Soreang -->
        <div class="col-lg-5 order-1 order-lg-2 text-center" data-aos="zoom-in" data-aos-delay="200">
          <div class="position-relative d-inline-block w-100">
            <div class="p-2 bg-white bg-opacity-15 rounded-4 border border-white border-opacity-30 shadow-2xl backdrop-blur">
              <img src="{{ !empty($siteInfo['hero_image']) ? asset('storage/' . $siteInfo['hero_image']) : asset('assets/home/img/soreang-hero.png') }}" class="img-fluid rounded-4 shadow-lg w-100 object-fit-cover" alt="Kantor {{ $siteInfo['kecamatan'] ?? 'Kecamatan Soreang' }}" style="max-height: 360px;">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 2. SECTION PROFIL & VISI MISI KECAMATAN SOREANG -->
  <section id="profil" class="py-5 bg-white">
    <div class="container py-3">
      
      @include('home.components.section-title', [
        'subtitle' => 'Profil & Arah Kebijakan',
        'title' => 'Profil & Visi Misi ' . ($siteInfo['kecamatan'] ?? 'Kecamatan Soreang'),
        'description' => 'Mengenal profil wilayah ' . ($siteInfo['kecamatan'] ?? 'Kecamatan Soreang') . ' ' . ($siteInfo['kota'] ?? 'Kota Parepare') . ' serta arah komitmen pelayanan publik.'
      ])

      <div class="row gy-4 align-items-stretch mt-3">
        
        <!-- Left: Profil Ringkas -->
        <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
          <div class="p-4 p-lg-5 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-primary text-white rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 58px; height: 58px; font-size: 1.75rem;">
                  <i class="bi bi-building-check"></i>
                </div>
                <div>
                  <h4 class="fw-bold text-dark m-0">{{ $siteInfo['kecamatan'] ?? 'Kecamatan Soreang' }}</h4>
                  <span class="text-primary fw-semibold small">{{ $siteInfo['kota'] ?? 'Kota Parepare' }} • Sulawesi Selatan</span>
                </div>
              </div>

              <p class="text-secondary leading-relaxed mb-4" style="font-size: 0.96rem; line-height: 1.7;">
                {{ $siteInfo['deskripsi_lengkap'] ?? 'Kecamatan Soreang merupakan salah satu kawasan pusat pemerintahan dan aktivitas ekonomi masyarakat di Kota Parepare.' }}
              </p>
            </div>

            <div class="row g-3 pt-3 border-top">
              <div class="col-6">
                <div class="p-3 bg-white rounded-4 border shadow-sm text-center">
                  <span class="d-block text-muted small fw-semibold mb-1">Kode Wilayah</span>
                  <h4 class="fw-bold text-primary m-0">{{ $siteInfo['kode_wilayah'] ?? '73.72.03' }}</h4>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 bg-white rounded-4 border shadow-sm text-center">
                  <span class="d-block text-muted small fw-semibold mb-1">Kelurahan</span>
                  <h4 class="fw-bold text-success m-0">7 Wilayah</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Cards Visi & Misi -->
        <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">
          <div class="d-flex flex-column gap-4 h-100">
            
            <!-- Visi Card -->
            <div class="p-4 p-lg-4 bg-white rounded-4 border border-primary-subtle shadow-sm">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">VISI UTAMA</span>
                <span class="small text-muted">{{ $siteInfo['kecamatan'] ?? 'Kecamatan Soreang' }}</span>
              </div>
              <h4 class="fw-bold text-dark mb-2">{{ $siteInfo['visi'] ?? '"Parepare Terkemuka & Soreang Smart Sejahtera"' }}</h4>
              <p class="text-secondary m-0 small" style="line-height: 1.6;">
                Mewujudkan tata kelola pemerintahan Kecamatan Soreang yang responsif, berbasis data digital, transparan, dan melayani masyarakat dengan integritas serta akuntabilitas tinggi.
              </p>
            </div>

            <!-- Misi Grid Cards -->
            <div class="row g-3 flex-grow-1">
              
              <div class="col-md-6">
                <div class="visi-misi-card p-4 bg-white shadow-sm h-100">
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-2 fw-semibold">Misi 01</span>
                  <h6 class="fw-bold text-dark mb-2">Pelayanan Publik Digital</h6>
                  <p class="text-muted small m-0" style="line-height: 1.6;">
                    Memberikan kepastian layanan administrasi surat online secara cepat, efisien, dan transparan bagi seluruh warga.
                  </p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="visi-misi-card p-4 bg-white shadow-sm h-100">
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-2 fw-semibold">Misi 02</span>
                  <h6 class="fw-bold text-dark mb-2">Pengaduan Responsif 24 Jam</h6>
                  <p class="text-muted small m-0" style="line-height: 1.6;">
                    Mewadahi aspirasi dan laporan keluhan warga se-Kecamatan Soreang dengan target penyelesaian terukur.
                  </p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="visi-misi-card p-4 bg-white shadow-sm h-100">
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-2 fw-semibold">Misi 03</span>
                  <h6 class="fw-bold text-dark mb-2">Integrasi 7 Kelurahan</h6>
                  <p class="text-muted small m-0" style="line-height: 1.6;">
                    Satu pangkalan data terpadu untuk mempermudah koordinasi administrasi lintas kelurahan Kecamatan Soreang.
                  </p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="visi-misi-card p-4 bg-white shadow-sm h-100">
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-2 fw-semibold">Misi 04</span>
                  <h6 class="fw-bold text-dark mb-2">Pemberdayaan Ekonomi UMKM</h6>
                  <p class="text-muted small m-0" style="line-height: 1.6;">
                    Mendorong potensi ekonomi kemasyarakatan dan kemudahan perizinan bagi pelaku UMKM lokal Soreang.
                  </p>
                </div>
              </div>

            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 3. SECTION JELAJAHI KECAMATAN SOREANG -->
  <section id="jelajahi" class="py-5 bg-light border-top border-bottom">
    <div class="container">
      
      @include('home.components.section-title', [
        'subtitle' => 'Jelajahi Services',
        'title' => 'Layanan & Portal Digital Kecamatan Soreang',
        'description' => 'Akses cepat modul pelayanan administrasi, pengaduan, dan portal publik se-Kecamatan Soreang.'
      ])

      <div class="row g-3 g-md-4 justify-content-center mt-2">
        
        {{-- Item 1: Smart Surat --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Smart Digital Service',
          'description' => 'Pengurusan 12 jenis surat keterangan online',
          'icon' => 'bi-file-earmark-text-fill',
          'badge' => 'Terpopuler',
          'isModal' => true,
          'modalTarget' => '#jenis-surat-modal',
          'delay' => 100,
          'color' => 'primary'
        ])

        {{-- Item 2: Smart Complaint --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Smart Complaint',
          'description' => 'Layanan pengaduan warga multi-channel',
          'icon' => 'bi-megaphone-fill',
          'badge' => 'Responsif',
          'link' => route('pengaduan.index'),
          'delay' => 150,
          'color' => 'danger'
        ])

        {{-- Item 3: Live Cek Status --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Cek Status Tiket',
          'description' => 'Lacak progres permohonan via NIK/Tiket',
          'icon' => 'bi-search',
          'badge' => 'Real-Time',
          'link' => route('cek-status.index'),
          'delay' => 200,
          'color' => 'success'
        ])

        {{-- Item 4: Jadwal Kelurahan --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Jadwal Kelurahan',
          'description' => 'Jam operasional & kontak 7 kelurahan',
          'icon' => 'bi-clock-history',
          'badge' => '7 Kelurahan',
          'isModal' => true,
          'modalTarget' => '#jadwal-pelayanan-modal',
          'delay' => 250,
          'color' => 'info'
        ])

        {{-- Item 5: QR Scanner --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Scan QR Code',
          'description' => 'Verifikasi keaslian resi & surat tiket',
          'icon' => 'bi-qr-code-scan',
          'badge' => 'Verifikasi',
          'isModal' => true,
          'modalTarget' => '#s3QrScannerModal',
          'delay' => 300,
          'color' => 'dark'
        ])

        {{-- Item 6: Peta Wilayah --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Peta Wilayah',
          'description' => 'Peta digital 7 kelurahan Soreang',
          'icon' => 'bi-map-fill',
          'badge' => 'Peta Leaflet',
          'link' => '#peta-soreang',
          'delay' => 350,
          'color' => 'warning'
        ])

        {{-- Item 7: Portal Berita --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Portal Berita',
          'description' => 'Informasi & agenda resmi kecamatan',
          'icon' => 'bi-newspaper',
          'badge' => 'Halaman Berita',
          'link' => route('berita.public.index'),
          'delay' => 400,
          'color' => 'primary'
        ])

        {{-- Item 8: Emergency 24/7 --}}
        @include('home.components.quick-grid-item', [
          'title' => 'Kontak Darurat',
          'description' => 'Tanggap darurat siaga 24/7',
          'icon' => 'bi-shield-exclamation',
          'badge' => 'Siaga 24/7',
          'link' => 'tel:112',
          'delay' => 450,
          'color' => 'danger'
        ])

      </div>
    </div>
  </section>

  <!-- 4. SECTION TANTANGAN & SOLUSI DIGITAL -->
  <section id="masalah-solusi" class="py-5 bg-white">
    
    @include('home.components.section-title', [
      'subtitle' => 'Tantangan & Solusi',
      'title' => 'Transformasi Digital Pelayanan Kecamatan',
      'description' => 'Menjawab tantangan administrasi kependudukan konvensional melalui platform 3S Soreang.'
    ])

    <div class="container mt-4">
      <div class="row gy-4 align-items-center">
        
        <!-- Left Column: Tantangan -->
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <div class="p-4 p-lg-5 bg-light rounded-4 border">
            <span class="badge bg-primary text-white px-3 py-1 rounded-pill fw-bold mb-3">Tantangan Birokrasi</span>
            <h4 class="fw-bold text-dark mb-4">Mengapa Diperlukan Soreang Smart Service?</h4>
            
            <div class="d-flex flex-column gap-3">
              @foreach($challenges as $index => $c)
                <div class="p-3 bg-white rounded-3 border shadow-sm">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.95rem;">
                      <span class="text-primary me-2 font-monospace">0{{ $index + 1 }}.</span> {{ $c['title'] }}
                    </h6>
                  </div>
                  <p class="text-muted small m-0 ps-4" style="line-height: 1.5;">{{ $c['description'] }}</p>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Right Column: Gambar Pelayanan Soreang -->
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
          <div class="ps-lg-3">
            
            <div class="position-relative mb-4">
              <div class="p-2 bg-white rounded-4 border shadow-sm">
                <img src="{{ !empty($siteInfo['service_image']) ? asset('storage/' . $siteInfo['service_image']) : asset('assets/home/img/soreang-service.png') }}" class="img-fluid rounded-4 w-100 object-fit-cover shadow-sm" alt="Pusat Pelayanan Publik {{ $siteInfo['kecamatan'] ?? 'Kecamatan Soreang' }}" style="max-height: 280px;">
              </div>
            </div>

            <div class="p-4 bg-white rounded-4 border shadow-sm position-relative overflow-hidden">
              <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-bold mb-2">{{ $siteInfo['service_badge'] ?? 'Solusi Terintegrasi' }}</span>
              <h4 class="fw-bold text-dark mb-2">{{ $siteInfo['service_title'] ?? 'Pelayanan Cepat, Transparan, & Tanpa Antri' }}</h4>
              <p class="text-muted small leading-relaxed mb-3">
                {{ $siteInfo['service_subtitle'] ?? ('Warga ' . ($siteInfo['kecamatan'] ?? 'Kecamatan Soreang') . ' kini dapat mengajukan surat keterangan online, melacak tiket status permohonan secara real-time, dan menyampaikan aspirasi tanpa harus datang mengantri di kantor kelurahan.') }}
              </p>
              <a href="#jenis-surat-modal" data-bs-toggle="modal" class="btn btn-primary text-white font-semibold rounded-pill px-4 py-2 hover-lift btn-sm">
                Pengajuan Online Now <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 5. SECTION FITUR UTAMA (8 SMART COMPONENTS) -->
  <section id="services" class="py-5 bg-light border-top border-bottom">
    
    @include('home.components.section-title', [
      'subtitle' => '8 Komponen Smart',
      'title' => 'Fitur Utama Soreang Smart Service',
      'description' => 'Modularitas layanan publik berbasis digital yang memudahkan warga dan aparatur kelurahan.'
    ])

    <div class="container mt-4">
      <div class="row gy-4">
        @foreach($smartComponents as $index => $comp)
          @include('home.components.service-card', [
            'title' => $comp['title'],
            'description' => $comp['description'],
            'icon' => $comp['bs_icon'],
            'badge' => $comp['badge'],
            'link' => $comp['link'],
            'linkText' => $comp['link_text'],
            'delay' => ($index + 1) * 100
          ])
        @endforeach
      </div>
    </div>
  </section>

  <!-- 6. SECTION PETA KECAMATAN SOREANG LEAFLET (DENGAN UPDATE POLIGON GARIS PER KELURAHAN) -->
  <section id="peta-soreang" class="py-5 bg-white">
    <div class="container" data-aos="fade-up">
      
      @include('home.components.section-title', [
        'subtitle' => 'Peta Digital Wilayah',
        'title' => 'Peta Administrasi Kecamatan Soreang',
        'description' => 'Visualisasi interaktif batas wilayah polygon & lokasi 7 Kelurahan se-Kecamatan Soreang Kota Parepare.'
      ])

      <div class="row gy-4 mt-2">
        
        <!-- Sidebar Filter & Detail Wilayah -->
        <div class="col-lg-4">
          <div class="p-4 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span id="s3KodeWilayahBadge" class="font-monospace small text-primary fw-bold bg-primary-subtle px-3 py-1 rounded-pill">Kode: 73.72.03</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle">Online Map</span>
              </div>

              <h5 id="s3SelectedKelurahanTitle" class="fw-bold text-dark mb-1">Kecamatan Soreang</h5>
              <p id="s3SelectedKelurahanDesc" class="text-muted small mb-4">Sesuai Kepmendagri No 300.2.2-2430 Tahun 2025</p>

              <!-- Dropdown Selector Kelurahan -->
              <div class="mb-4">
                <label class="form-label fw-bold small text-secondary">PILIH DESA / KELURAHAN:</label>
                <select id="soreangKelurahanSelect" class="form-select form-select-lg rounded-pill fs-6 border-primary shadow-sm fw-semibold">
                  <option value="all">-- Semua Kelurahan (Soreang) --</option>
                  <option value="bukit_harapan">Kelurahan Bukit Harapan</option>
                  <option value="bukit_indah">Kelurahan Bukit Indah</option>
                  <option value="kampung_pisang">Kelurahan Kampung Pisang</option>
                  <option value="lakessi">Kelurahan Lakessi</option>
                  <option value="ujung_baru">Kelurahan Ujung Baru</option>
                  <option value="ujung_lare">Kelurahan Ujung Lare</option>
                  <option value="watang_soreang">Kelurahan Watang Soreang</option>
                </select>
              </div>

              <!-- Dynamic Information Grid -->
              <div class="p-3 bg-white rounded-4 border shadow-sm mb-3">
                <div class="d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">PROVINSI</span>
                  <span class="fw-bold small text-dark">Sulawesi Selatan</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">KOTA / KABUPATEN</span>
                  <span class="fw-bold small text-dark">Kota Parepare</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">KECAMATAN</span>
                  <span class="fw-bold small text-primary">Soreang</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">DESA / KELURAHAN</span>
                  <span id="s3SelectedKelurahanDetail" class="fw-bold small text-primary">Semua Kelurahan</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                  <span class="text-muted small">KOORDINAT PUSAT</span>
                  <span id="s3SelectedKoordinat" class="font-monospace small text-dark fw-semibold">-3.98924, 119.64297</span>
                </div>
              </div>
            </div>

            <div class="pt-2">
              <a href="{{ route('berita.public.index') }}" class="btn btn-outline-primary rounded-pill w-100 fw-semibold btn-sm">
                <i class="bi bi-newspaper me-1"></i> Lihat Portal Berita & Agenda
              </a>
            </div>

          </div>
        </div>

        <!-- Leaflet Map Container -->
        <div class="col-lg-8">
          <div class="p-2 bg-white rounded-4 border shadow-sm position-relative">
            <div id="soreangMap" class="rounded-4"></div>
            
            <!-- Floating Map Footer Bar (Matches Wilayah ID Screenshot) -->
            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 z-3 shadow-lg rounded-pill overflow-hidden">
              <div id="s3MapFooterKode" class="bg-dark bg-opacity-90 text-white font-monospace rounded-pill px-4 py-2 small border border-secondary shadow backdrop-blur" style="font-size: 0.82rem;">
                KODE <strong>73.72.03</strong> &nbsp; LAT <strong>-3.98924</strong> &nbsp; LNG <strong>119.64297</strong>
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mt-2 px-2">
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Pilih kelurahan di dropdown untuk memperbarui garis batas polygon & lokasi kantor.</small>
            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.75rem;">Leaflet.js + OpenStreetMap</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 7. SECTION INDIKATOR KEBERHASILAN (STATS COUNTER) -->
  <section id="indikator" class="py-5 bg-primary text-white position-relative">
    <div class="container" data-aos="fade-up">
      <div class="row gy-4">
        @foreach($metrics as $index => $m)
          @include('home.components.stat-card', [
            'count' => $m['count'],
            'suffix' => $m['suffix'],
            'label' => $m['label'],
            'description' => $m['description'],
            'icon' => $m['icon'],
            'delay' => ($index + 1) * 100
          ])
        @endforeach
      </div>
    </div>
  </section>

  <!-- 8. SECTION GALERI / SCREENSHOT SISTEM -->
  <section id="galeri" class="py-5 bg-light border-bottom">
    
    @include('home.components.section-title', [
      'subtitle' => 'Antarmuka',
      'title' => 'Galeri & Screenshot Fitur 3S',
      'description' => 'Tampilan antarmuka sistem yang bersih, cepat, dan mudah digunakan.'
    ])

    <div class="container mt-4">
      <div class="row gy-4">
        @foreach($screenshots as $index => $shot)
          <div class="col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
            <div class="p-4 bg-white border rounded-4 shadow-sm h-100 hover-lift transition-all">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge bg-primary rounded-pill px-3 py-2">{{ $shot['tag'] }}</span>
                <small class="text-muted font-monospace">Modul {{ $index + 1 }}</small>
              </div>
              <h5 class="fw-bold text-dark fs-6 mb-2">{{ $shot['title'] }}</h5>
              <p class="text-muted small mb-0">{{ $shot['description'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>



  {{-- Include All Interactive Modals --}}
  @include('home.partials.modals')

@endsection

@push('scripts')
<!-- Leaflet JS for Map -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
  (function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
      const mapContainer = document.getElementById('soreangMap');
      if (!mapContainer) return;

      // Map initialization - Soreang Center
      const soreangCenter = [-3.98924, 119.64297];
      const map = L.map('soreangMap', {
        zoomControl: true,
        scrollWheelZoom: false
      }).setView(soreangCenter, 13);

      // OpenStreetMap Tiles (Wilayah ID style)
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Wilayah ID 73.72.03'
      }).addTo(map);

      // Data High-Precision GeoJSON 7 Kelurahan + Kecamatan Soreang
      const kelurahanData = {
        all: {
          name: "Kecamatan Soreang",
          kode: "73.72.03",
          lat: "-3.98924",
          lng: "119.64297",
          coords: [-3.98924, 119.64297],
          desc: "Batas Wilayah Administrasi Kecamatan Soreang, Kota Parepare",
          polygon: [[-4.00769,119.6231],[-4.00778,119.6233],[-4.00806,119.62622],[-4.0104,119.62604],[-4.0107,119.62854],[-4.01077,119.62922],[-4.01091,119.62949],[-4.01108,119.62967],[-4.01125,119.62995],[-4.01135,119.63012],[-4.01145,119.63018],[-4.0114,119.63027],[-4.0111,119.63058],[-4.01087,119.63098],[-4.01055,119.63146],[-4.01026,119.63206],[-4.00987,119.63254],[-4.00978,119.63279],[-4.00984,119.633],[-4.00995,119.63316],[-4.01009,119.63323],[-4.01046,119.63326],[-4.01072,119.63331],[-4.01102,119.63352],[-4.01116,119.63418],[-4.01142,119.63566],[-4.01123,119.63602],[-4.01069,119.63661],[-4.01038,119.63701],[-4.00982,119.63787],[-4.00964,119.63803],[-4.00936,119.63813],[-4.00908,119.63817],[-4.00862,119.63811],[-4.00807,119.63812],[-4.00711,119.63858],[-4.00676,119.63878],[-4.00654,119.63892],[-4.0063,119.6392],[-4.00585,119.6404],[-4.00523,119.64126],[-4.00213,119.64532],[-4.00177,119.64568],[-4.00054,119.64642],[-4.00018,119.64675],[-3.99857,119.6476],[-3.99782,119.648],[-3.99561,119.64956],[-3.99456,119.65006],[-3.99296,119.65114],[-3.99226,119.65168],[-3.99108,119.65199],[-3.98961,119.6523],[-3.98877,119.65236],[-3.98707,119.65243],[-3.98515,119.65252],[-3.9847,119.65242],[-3.98317,119.65165],[-3.98274,119.65159],[-3.98218,119.65164],[-3.98182,119.65176],[-3.98156,119.65194],[-3.98121,119.65232],[-3.98071,119.65346],[-3.98039,119.65412],[-3.98009,119.65447],[-3.97976,119.65468],[-3.97956,119.65476],[-3.97932,119.65484],[-3.97885,119.6549],[-3.97766,119.65503],[-3.97645,119.65524],[-3.97591,119.65544],[-3.97543,119.65581],[-3.97471,119.65646],[-3.97393,119.6571],[-3.97267,119.65773],[-3.97171,119.65848],[-3.97104,119.65917],[-3.97046,119.66036],[-3.97,119.66097],[-3.96948,119.66159],[-3.96933,119.66165],[-3.96904,119.66169],[-3.96879,119.66156],[-3.96831,119.66124],[-3.96796,119.6612],[-3.96756,119.66149],[-3.96659,119.66357],[-3.96604,119.66404],[-3.96543,119.66449],[-3.96503,119.66469],[-3.96458,119.66483],[-3.9643,119.66507],[-3.96419,119.66536],[-3.96419,119.66584],[-3.96424,119.66622],[-3.96386,119.66612],[-3.96324,119.66587],[-3.96263,119.66547],[-3.96205,119.66493],[-3.96117,119.66404],[-3.96054,119.66336],[-3.96094,119.66275],[-3.96189,119.66151],[-3.9638,119.65888],[-3.96505,119.657],[-3.96562,119.65621],[-3.96729,119.65417],[-3.96784,119.65339],[-3.9684,119.65315],[-3.96874,119.65288],[-3.96894,119.65268],[-3.96946,119.65187],[-3.96978,119.65152],[-3.96983,119.65141],[-3.96985,119.65125],[-3.96986,119.65105],[-3.96982,119.65066],[-3.97115,119.6489],[-3.97122,119.64886],[-3.97136,119.64873],[-3.97147,119.64859],[-3.97158,119.64843],[-3.9717,119.64822],[-3.97184,119.648],[-3.97186,119.64798],[-3.97277,119.64674],[-3.97325,119.64668],[-3.97353,119.64659],[-3.97377,119.64645],[-3.97465,119.64604],[-3.97489,119.64588],[-3.97502,119.64576],[-3.97509,119.64563],[-3.9752,119.64532],[-3.97524,119.64481],[-3.97539,119.64442],[-3.97567,119.64392],[-3.97604,119.64338],[-3.97639,119.64291],[-3.9765,119.64279],[-3.97707,119.64236],[-3.97741,119.64206],[-3.97756,119.64197],[-3.97814,119.64173],[-3.97842,119.64155],[-3.97868,119.64134],[-3.97927,119.64098],[-3.97958,119.64071],[-3.97996,119.6405],[-3.98051,119.6401],[-3.98066,119.63973],[-3.98075,119.63939],[-3.9808,119.63931],[-3.98087,119.63925],[-3.98054,119.63869],[-3.98045,119.63866],[-3.9803,119.63866],[-3.98019,119.63864],[-3.98004,119.6385],[-3.9798,119.63817],[-3.97977,119.63804],[-3.97977,119.63794],[-3.97983,119.63772],[-3.97994,119.63756],[-3.97996,119.63725],[-3.98003,119.63713],[-3.98029,119.63684],[-3.98044,119.63674],[-3.98074,119.63663],[-3.98082,119.63658],[-3.98091,119.63647],[-3.98095,119.6363],[-3.98109,119.63616],[-3.98152,119.63606],[-3.9818,119.63604],[-3.98212,119.63607],[-3.98225,119.63605],[-3.98229,119.63596],[-3.98231,119.63548],[-3.98235,119.63522],[-3.98249,119.63477],[-3.98262,119.6348],[-3.98282,119.6341],[-3.98293,119.63413],[-3.98302,119.63409],[-3.9831,119.63407],[-3.9832,119.6341],[-3.98328,119.63413],[-3.98346,119.63412],[-3.98353,119.63393],[-3.98445,119.63426],[-3.98535,119.63461],[-3.98547,119.63464],[-3.98582,119.63472],[-3.98617,119.63475],[-3.98636,119.63475],[-3.9869,119.63455],[-3.98732,119.63434],[-3.98737,119.63432],[-3.98752,119.63424],[-3.98792,119.63407],[-3.98842,119.63383],[-3.9894,119.63348],[-3.99,119.63361],[-3.99036,119.63366],[-3.99032,119.63299],[-3.99111,119.63286],[-3.99179,119.63272],[-3.99182,119.63316],[-3.99183,119.63337],[-3.99232,119.63335],[-3.99353,119.63302],[-3.99352,119.63289],[-3.99352,119.63284],[-3.99368,119.63286],[-3.99396,119.63288],[-3.99413,119.63286],[-3.99488,119.63266],[-3.99656,119.63223],[-3.99712,119.63195],[-3.9983,119.6311],[-3.99896,119.63086],[-3.99991,119.63049],[-4.00046,119.63027],[-4.001,119.63004],[-4.00175,119.6296],[-4.00195,119.62941],[-4.00229,119.62901],[-4.00241,119.62884],[-4.00266,119.62852],[-4.00277,119.6284],[-4.00285,119.62831],[-4.0032,119.62766],[-4.00345,119.62717],[-4.00355,119.62696],[-4.0036,119.62681],[-4.00372,119.62635],[-4.00382,119.62594],[-4.00385,119.62584],[-4.00408,119.62522],[-4.00424,119.62475],[-4.00433,119.62466],[-4.00417,119.62441],[-4.00411,119.62436],[-4.00385,119.62431],[-4.00369,119.62424],[-4.00361,119.62411],[-4.00404,119.62388],[-4.00412,119.62403],[-4.00439,119.62391],[-4.0046,119.62379],[-4.00459,119.62376],[-4.00515,119.62366],[-4.00662,119.62331],[-4.00769,119.6231]]
        },
        bukit_harapan: {
          name: "Kelurahan Bukit Harapan",
          kode: "73.72.03.1007",
          lat: "-3.98226",
          lng: "119.64753",
          coords: [-3.98226, 119.64753],
          desc: "Kantor Kelurahan Bukit Harapan, Kecamatan Soreang, Kota Parepare",
          polygon: [[-3.98323,119.63546],[-3.98337,119.63547],[-3.98366,119.63548],[-3.98383,119.63549],[-3.98411,119.6355],[-3.9843,119.63553],[-3.98442,119.63556],[-3.98457,119.63561],[-3.98486,119.6357],[-3.98502,119.63575],[-3.98518,119.63579],[-3.98582,119.63596],[-3.98593,119.636],[-3.98609,119.63607],[-3.98632,119.63621],[-3.98641,119.63626],[-3.98654,119.63635],[-3.98695,119.63663],[-3.98731,119.63689],[-3.98779,119.63722],[-3.98794,119.63732],[-3.98827,119.63754],[-3.98836,119.63759],[-3.98844,119.63763],[-3.98856,119.63765],[-3.98882,119.63768],[-3.98904,119.63771],[-3.98928,119.63774],[-3.98944,119.63774],[-3.98962,119.63772],[-3.9899,119.63765],[-3.99018,119.63757],[-3.99023,119.63755],[-3.99057,119.63743],[-3.99083,119.63735],[-3.99091,119.63732],[-3.99114,119.63727],[-3.99126,119.63725],[-3.99142,119.63723],[-3.99177,119.63724],[-3.99217,119.63724],[-3.99226,119.63725],[-3.99264,119.63729],[-3.9928,119.6373],[-3.99306,119.63726],[-3.99333,119.6372],[-3.99372,119.63712],[-3.99407,119.63704],[-3.99417,119.63702],[-3.99462,119.63692],[-3.99518,119.63681],[-3.9954,119.63676],[-3.99569,119.6367],[-3.9959,119.63665],[-3.99605,119.63661],[-3.99623,119.63656],[-3.99647,119.63646],[-3.99652,119.63659],[-3.99657,119.63666],[-3.99663,119.63674],[-3.9968,119.63689],[-3.99687,119.63696],[-3.99689,119.63698],[-3.9969,119.63702],[-3.9969,119.63709],[-3.99689,119.63727],[-3.99684,119.63768],[-3.99681,119.63799],[-3.99681,119.63829],[-3.99682,119.63841],[-3.99682,119.63845],[-3.99683,119.63851],[-3.99693,119.63854],[-3.9972,119.63863],[-3.99738,119.6387],[-3.99743,119.63873],[-3.99748,119.63876],[-3.99752,119.63879],[-3.99853,119.63972],[-3.99856,119.63974],[-3.99858,119.63978],[-3.99861,119.63982],[-3.99863,119.63987],[-3.99866,119.63995],[-3.99868,119.64005],[-3.99872,119.64064],[-3.99876,119.64098],[-3.99878,119.64106],[-3.9988,119.64111],[-3.99894,119.64138],[-3.99898,119.64142],[-3.9993,119.64174],[-3.99912,119.64193],[-3.99903,119.64198],[-3.99878,119.64208],[-3.99848,119.64219],[-3.99837,119.64226],[-3.99833,119.64229],[-3.9983,119.64233],[-3.99826,119.64241],[-3.9982,119.64276],[-3.99815,119.64303],[-3.99811,119.64336],[-3.99808,119.64367],[-3.99805,119.64402],[-3.99804,119.64427],[-3.99802,119.64447],[-3.99801,119.64457],[-3.998,119.64462],[-3.99797,119.64469],[-3.99794,119.64477],[-3.99787,119.64486],[-3.99774,119.64504],[-3.99767,119.64515],[-3.99763,119.64526],[-3.9976,119.64537],[-3.99758,119.64549],[-3.99755,119.64575],[-3.99755,119.64579],[-3.99755,119.64595],[-3.99755,119.64599],[-3.99757,119.64605],[-3.9976,119.64612],[-3.99775,119.64634],[-3.99857,119.6476],[-3.99782,119.648],[-3.99561,119.64956],[-3.99456,119.65006],[-3.99296,119.65114],[-3.99226,119.65168],[-3.99108,119.65199],[-3.98961,119.6523],[-3.98877,119.65236],[-3.98707,119.65243],[-3.98515,119.65252],[-3.9847,119.65242],[-3.98317,119.65165],[-3.98274,119.65159],[-3.98218,119.65164],[-3.98182,119.65176],[-3.98156,119.65194],[-3.98121,119.65232],[-3.98071,119.65346],[-3.98039,119.65412],[-3.98009,119.65447],[-3.97976,119.65468],[-3.97956,119.65476],[-3.97932,119.65484],[-3.97885,119.6549],[-3.97766,119.65503],[-3.97645,119.65524],[-3.97591,119.65544],[-3.97543,119.65581],[-3.97471,119.65646],[-3.97393,119.6571],[-3.97267,119.65773],[-3.97171,119.65848],[-3.97104,119.65917],[-3.97046,119.66036],[-3.97,119.66097],[-3.96948,119.66159],[-3.96933,119.66165],[-3.96904,119.66169],[-3.96879,119.66156],[-3.96831,119.66124],[-3.96796,119.6612],[-3.96756,119.66149],[-3.96659,119.66357],[-3.96604,119.66404],[-3.96543,119.66449],[-3.96503,119.66469],[-3.96458,119.66483],[-3.9643,119.66507],[-3.96419,119.66536],[-3.96419,119.66584],[-3.96424,119.66622],[-3.96386,119.66612],[-3.96324,119.66587],[-3.96263,119.66547],[-3.96205,119.66493],[-3.96117,119.66404],[-3.96054,119.66336],[-3.96094,119.66275],[-3.96189,119.66151],[-3.9638,119.65888],[-3.96505,119.657],[-3.96562,119.65621],[-3.96729,119.65417],[-3.96784,119.65339],[-3.9684,119.65315],[-3.96874,119.65288],[-3.96894,119.65268],[-3.96946,119.65187],[-3.96978,119.65152],[-3.96983,119.65141],[-3.96985,119.65125],[-3.96986,119.65105],[-3.96982,119.65066],[-3.97115,119.6489],[-3.97122,119.64886],[-3.97136,119.64873],[-3.97147,119.64859],[-3.97158,119.64843],[-3.9717,119.64822],[-3.97184,119.648],[-3.97186,119.64798],[-3.97277,119.64674],[-3.97325,119.64668],[-3.97353,119.64659],[-3.97377,119.64645],[-3.97465,119.64604],[-3.97489,119.64588],[-3.97502,119.64576],[-3.97509,119.64563],[-3.9752,119.64532],[-3.97524,119.64481],[-3.97539,119.64442],[-3.97567,119.64392],[-3.97604,119.64338],[-3.97639,119.64291],[-3.9765,119.64279],[-3.97707,119.64236],[-3.97741,119.64206],[-3.97756,119.64197],[-3.97814,119.64173],[-3.97842,119.64155],[-3.97868,119.64134],[-3.97927,119.64098],[-3.97958,119.64071],[-3.97996,119.6405],[-3.98051,119.6401],[-3.98066,119.63973],[-3.98075,119.63939],[-3.9808,119.63931],[-3.98087,119.63925],[-3.98054,119.63869],[-3.98045,119.63866],[-3.9803,119.63866],[-3.98019,119.63864],[-3.98004,119.6385],[-3.9798,119.63817],[-3.97977,119.63804],[-3.97977,119.63794],[-3.97983,119.63772],[-3.97994,119.63756],[-3.97996,119.63725],[-3.98003,119.63713],[-3.98029,119.63684],[-3.98044,119.63674],[-3.98074,119.63663],[-3.98082,119.63658],[-3.98091,119.63647],[-3.98095,119.6363],[-3.98109,119.63616],[-3.98152,119.63606],[-3.9818,119.63604],[-3.98212,119.63607],[-3.98225,119.63605],[-3.98229,119.63596],[-3.98231,119.63548],[-3.98244,119.63548],[-3.98273,119.63547],[-3.98323,119.63546]]
        },
        bukit_indah: {
          name: "Kelurahan Bukit Indah",
          kode: "73.72.03.1006",
          lat: "-3.99612",
          lng: "119.64185",
          coords: [-3.99612, 119.64185],
          desc: "Kantor Kelurahan Bukit Indah, Kecamatan Soreang, Kota Parepare",
          polygon: [[-4.0046,119.63],[-4.00464,119.63008],[-4.00466,119.63013],[-4.00468,119.63016],[-4.00472,119.63042],[-4.00475,119.63061],[-4.00479,119.63079],[-4.00483,119.63093],[-4.00489,119.63108],[-4.00494,119.6312],[-4.00509,119.6315],[-4.00518,119.63169],[-4.00523,119.63181],[-4.00543,119.63214],[-4.00558,119.63239],[-4.00566,119.63253],[-4.00575,119.63267],[-4.00607,119.63314],[-4.00647,119.6335],[-4.00618,119.63363],[-4.00614,119.63366],[-4.00588,119.63385],[-4.0062,119.63492],[-4.00614,119.63511],[-4.00618,119.63534],[-4.00586,119.6354],[-4.00599,119.63583],[-4.00568,119.63595],[-4.00554,119.63608],[-4.00545,119.63604],[-4.00515,119.6363],[-4.00531,119.63663],[-4.00581,119.63707],[-4.00576,119.6371],[-4.0056,119.63719],[-4.00531,119.63734],[-4.00479,119.63762],[-4.00432,119.63788],[-4.00455,119.63842],[-4.00466,119.63877],[-4.00472,119.6388],[-4.00532,119.63901],[-4.00554,119.63908],[-4.0057,119.63913],[-4.00577,119.63914],[-4.00585,119.63915],[-4.00605,119.63917],[-4.00624,119.63918],[-4.0063,119.6392],[-4.00585,119.6404],[-4.00523,119.64126],[-4.00213,119.64532],[-4.00177,119.64568],[-4.00054,119.64642],[-4.00018,119.64675],[-3.99857,119.6476],[-3.99775,119.64634],[-3.9976,119.64612],[-3.99757,119.64605],[-3.99755,119.64599],[-3.99755,119.64595],[-3.99755,119.64579],[-3.99755,119.64575],[-3.99758,119.64549],[-3.9976,119.64537],[-3.99763,119.64526],[-3.99767,119.64515],[-3.99774,119.64504],[-3.99787,119.64486],[-3.99794,119.64477],[-3.99797,119.64469],[-3.998,119.64462],[-3.99801,119.64457],[-3.99802,119.64447],[-3.99804,119.64427],[-3.99805,119.64402],[-3.99808,119.64367],[-3.99811,119.64336],[-3.99815,119.64303],[-3.9982,119.64276],[-3.99826,119.64241],[-3.9983,119.64233],[-3.99833,119.64229],[-3.99837,119.64226],[-3.99848,119.64219],[-3.99878,119.64208],[-3.99903,119.64198],[-3.99912,119.64193],[-3.9993,119.64174],[-3.99898,119.64142],[-3.99894,119.64138],[-3.9988,119.64111],[-3.99878,119.64106],[-3.99876,119.64098],[-3.99872,119.64064],[-3.99868,119.64005],[-3.99866,119.63995],[-3.99863,119.63987],[-3.99861,119.63982],[-3.99858,119.63978],[-3.99856,119.63974],[-3.99853,119.63972],[-3.99752,119.63879],[-3.99748,119.63876],[-3.99743,119.63873],[-3.99738,119.6387],[-3.9972,119.63863],[-3.99693,119.63854],[-3.99683,119.63851],[-3.99682,119.63845],[-3.99682,119.63841],[-3.99681,119.63829],[-3.99681,119.63799],[-3.99684,119.63768],[-3.99689,119.63727],[-3.9969,119.63709],[-3.9969,119.63702],[-3.99689,119.63698],[-3.99687,119.63696],[-3.9968,119.63689],[-3.99663,119.63674],[-3.99657,119.63666],[-3.99652,119.63659],[-3.99647,119.63646],[-3.99659,119.63641],[-3.99665,119.63639],[-3.99697,119.63624],[-3.99712,119.63618],[-3.9973,119.63612],[-3.9975,119.63603],[-3.99766,119.63594],[-3.99782,119.63585],[-3.99829,119.63545],[-3.99844,119.63534],[-3.99877,119.63511],[-3.99919,119.6348],[-3.99947,119.63459],[-3.99962,119.63448],[-3.99987,119.63431],[-4.00034,119.63398],[-4.00051,119.63384],[-4.00067,119.63367],[-4.00073,119.6336],[-4.00078,119.63354],[-4.001,119.63327],[-4.00131,119.63294],[-4.00145,119.63278],[-4.00166,119.63256],[-4.00178,119.63242],[-4.00194,119.63226],[-4.00204,119.63214],[-4.00216,119.63202],[-4.0022,119.63199],[-4.00237,119.63186],[-4.00267,119.63163],[-4.00288,119.63145],[-4.00311,119.63125],[-4.00334,119.63106],[-4.00373,119.63073],[-4.00396,119.63054],[-4.00443,119.63013],[-4.0045,119.63007],[-4.00455,119.63003],[-4.0046,119.63]]
        },
        kampung_pisang: {
          name: "Kelurahan Kampung Pisang",
          kode: "73.72.03.1001",
          lat: "-3.99505",
          lng: "119.63180",
          coords: [-3.99505, 119.63180],
          desc: "Kantor Kelurahan Kampung Pisang, Kecamatan Soreang, Kota Parepare",
          polygon: [[-4.00769,119.6231],[-4.00778,119.6233],[-4.00806,119.62622],[-4.00794,119.62624],[-4.00687,119.62639],[-4.00637,119.62647],[-4.00626,119.62647],[-4.0059,119.62646],[-4.0057,119.62645],[-4.00556,119.62646],[-4.00468,119.62648],[-4.0047,119.62619],[-4.0047,119.62601],[-4.00469,119.62589],[-4.00467,119.62579],[-4.00465,119.62571],[-4.00463,119.62568],[-4.0046,119.62568],[-4.00436,119.62575],[-4.00426,119.62579],[-4.00424,119.62581],[-4.00423,119.62586],[-4.00393,119.62585],[-4.00385,119.62584],[-4.00408,119.62522],[-4.00424,119.62475],[-4.00433,119.62466],[-4.00417,119.62441],[-4.00411,119.62436],[-4.00385,119.62431],[-4.00369,119.62424],[-4.00361,119.62411],[-4.00404,119.62388],[-4.00412,119.62403],[-4.00439,119.62391],[-4.0046,119.62379],[-4.00459,119.62376],[-4.00515,119.62366],[-4.00662,119.62331],[-4.00769,119.6231]]
        },
        lakessi: {
          name: "Kelurahan Lakessi",
          kode: "73.72.03.1002",
          lat: "-4.00451",
          lng: "119.62688",
          coords: [-4.00451, 119.62688],
          desc: "Kantor Kelurahan Lakessi, Kecamatan Soreang, Kota Parepare",
          polygon: [[-4.0046,119.62568],[-4.00463,119.62568],[-4.00465,119.62571],[-4.00467,119.62579],[-4.00469,119.62589],[-4.0047,119.62601],[-4.0047,119.62619],[-4.00468,119.62648],[-4.00556,119.62646],[-4.0057,119.62645],[-4.0059,119.62646],[-4.00626,119.62647],[-4.00637,119.62647],[-4.00687,119.62639],[-4.00794,119.62624],[-4.00806,119.62622],[-4.00807,119.62625],[-4.00821,119.62783],[-4.00821,119.62785],[-4.0078,119.6279],[-4.00733,119.62792],[-4.00721,119.62796],[-4.00732,119.62897],[-4.00729,119.62897],[-4.00655,119.62905],[-4.00637,119.62907],[-4.00647,119.62974],[-4.00575,119.62983],[-4.00548,119.62987],[-4.00539,119.62988],[-4.00506,119.62992],[-4.00485,119.62995],[-4.0048,119.62996],[-4.00477,119.62996],[-4.00474,119.62996],[-4.00472,119.62996],[-4.0047,119.62996],[-4.00468,119.62996],[-4.00466,119.62997],[-4.00462,119.62998],[-4.0046,119.63],[-4.00455,119.63003],[-4.0045,119.63007],[-4.00426,119.62982],[-4.00398,119.6295],[-4.00347,119.62888],[-4.00329,119.6287],[-4.00319,119.62862],[-4.0029,119.62846],[-4.00277,119.6284],[-4.00285,119.62831],[-4.0032,119.62766],[-4.00345,119.62717],[-4.00355,119.62696],[-4.0036,119.62681],[-4.00372,119.62635],[-4.00382,119.62594],[-4.00385,119.62584],[-4.00393,119.62585],[-4.00423,119.62586],[-4.00424,119.62581],[-4.00426,119.62579],[-4.00436,119.62575],[-4.0046,119.62568]]
        },
        ujung_baru: {
          name: "Kelurahan Ujung Baru",
          kode: "73.72.03.1005",
          lat: "-4.00015",
          lng: "119.63351",
          coords: [-4.00015, 119.63351],
          desc: "Kantor Kelurahan Ujung Baru, Kecamatan Soreang, Kota Parepare",
          polygon: [[-4.0104,119.62604],[-4.0107,119.62854],[-4.01077,119.62922],[-4.01091,119.62949],[-4.01108,119.62967],[-4.01125,119.62995],[-4.01135,119.63012],[-4.01145,119.63018],[-4.0114,119.63027],[-4.0111,119.63058],[-4.01087,119.63098],[-4.01055,119.63146],[-4.01026,119.63206],[-4.00987,119.63254],[-4.00978,119.63279],[-4.00984,119.633],[-4.00995,119.63316],[-4.01009,119.63323],[-4.01046,119.63326],[-4.01072,119.63331],[-4.01102,119.63352],[-4.01116,119.63418],[-4.01142,119.63566],[-4.01123,119.63602],[-4.01069,119.63661],[-4.01038,119.63701],[-4.00982,119.63787],[-4.00964,119.63803],[-4.00936,119.63813],[-4.00908,119.63817],[-4.00862,119.63811],[-4.00807,119.63812],[-4.00711,119.63858],[-4.00703,119.63813],[-4.00697,119.63794],[-4.00695,119.63787],[-4.00692,119.63782],[-4.00684,119.63767],[-4.00681,119.63762],[-4.00678,119.63758],[-4.00669,119.63744],[-4.00664,119.63739],[-4.00614,119.63684],[-4.00567,119.63631],[-4.00561,119.63623],[-4.00559,119.63619],[-4.00559,119.63615],[-4.00559,119.63612],[-4.00561,119.63607],[-4.00564,119.63603],[-4.00571,119.63599],[-4.00624,119.63578],[-4.00627,119.63576],[-4.00631,119.63573],[-4.00636,119.63569],[-4.00642,119.63563],[-4.00649,119.63553],[-4.00655,119.63545],[-4.00659,119.63542],[-4.00662,119.63539],[-4.00717,119.63506],[-4.00724,119.63499],[-4.00731,119.63489],[-4.00773,119.63423],[-4.00777,119.63412],[-4.00779,119.63406],[-4.0078,119.63401],[-4.0078,119.6338],[-4.00764,119.63243],[-4.0081,119.63232],[-4.00814,119.63231],[-4.0082,119.6323],[-4.00827,119.63228],[-4.00848,119.63222],[-4.00857,119.63218],[-4.00863,119.63214],[-4.00867,119.63207],[-4.00866,119.632],[-4.00863,119.63181],[-4.0086,119.63151],[-4.00851,119.631],[-4.00847,119.63056],[-4.00838,119.62964],[-4.00836,119.62947],[-4.00832,119.62885],[-4.00822,119.62787],[-4.00821,119.62785],[-4.00821,119.62783],[-4.00807,119.62625],[-4.00806,119.62622],[-4.0104,119.62604]]
        },
        ujung_lare: {
          name: "Kelurahan Ujung Lare",
          kode: "73.72.03.1004",
          lat: "-3.99182",
          lng: "119.63412",
          coords: [-3.99182, 119.63412],
          desc: "Kantor Kelurahan Ujung Lare, Kecamatan Soreang, Kota Parepare",
          polygon: [[-4.00821,119.62785],[-4.00822,119.62787],[-4.00832,119.62885],[-4.00836,119.62947],[-4.00838,119.62964],[-4.00847,119.63056],[-4.00851,119.631],[-4.0086,119.63151],[-4.00863,119.63181],[-4.00866,119.632],[-4.00867,119.63207],[-4.00863,119.63214],[-4.00857,119.63218],[-4.00848,119.63222],[-4.00827,119.63228],[-4.0082,119.6323],[-4.00814,119.63231],[-4.0081,119.63232],[-4.00764,119.63243],[-4.0078,119.6338],[-4.0078,119.63401],[-4.00779,119.63406],[-4.00777,119.63412],[-4.00773,119.63423],[-4.00731,119.63489],[-4.00724,119.63499],[-4.00717,119.63506],[-4.00662,119.63539],[-4.00659,119.63542],[-4.00655,119.63545],[-4.00649,119.63553],[-4.00642,119.63563],[-4.00636,119.63569],[-4.00631,119.63573],[-4.00627,119.63576],[-4.00624,119.63578],[-4.00571,119.63599],[-4.00564,119.63603],[-4.00561,119.63607],[-4.00559,119.63612],[-4.00559,119.63615],[-4.00559,119.63619],[-4.00561,119.63623],[-4.00567,119.63631],[-4.00614,119.63684],[-4.00664,119.63739],[-4.00669,119.63744],[-4.00678,119.63758],[-4.00681,119.63762],[-4.00684,119.63767],[-4.00692,119.63782],[-4.00695,119.63787],[-4.00697,119.63794],[-4.00703,119.63813],[-4.00711,119.63858],[-4.00676,119.63878],[-4.00654,119.63892],[-4.0063,119.6392],[-4.00624,119.63918],[-4.00605,119.63917],[-4.00585,119.63915],[-4.00577,119.63914],[-4.0057,119.63913],[-4.00554,119.63908],[-4.00532,119.63901],[-4.00472,119.6388],[-4.00466,119.63877],[-4.00455,119.63842],[-4.00432,119.63788],[-4.00479,119.63762],[-4.00531,119.63734],[-4.0056,119.63719],[-4.00576,119.6371],[-4.00581,119.63707],[-4.00531,119.63663],[-4.00515,119.6363],[-4.00545,119.63604],[-4.00554,119.63608],[-4.00568,119.63595],[-4.00599,119.63583],[-4.00586,119.6354],[-4.00618,119.63534],[-4.00614,119.63511],[-4.0062,119.63492],[-4.00588,119.63385],[-4.00614,119.63366],[-4.00618,119.63363],[-4.00647,119.6335],[-4.00607,119.63314],[-4.00575,119.63267],[-4.00566,119.63253],[-4.00558,119.63239],[-4.00543,119.63214],[-4.00523,119.63181],[-4.00518,119.63169],[-4.00509,119.6315],[-4.00494,119.6312],[-4.00489,119.63108],[-4.00483,119.63093],[-4.00479,119.63079],[-4.00475,119.63061],[-4.00472,119.63042],[-4.00468,119.63016],[-4.00466,119.63013],[-4.00464,119.63008],[-4.0046,119.63],[-4.00462,119.62998],[-4.00466,119.62997],[-4.00468,119.62996],[-4.0047,119.62996],[-4.00472,119.62996],[-4.00474,119.62996],[-4.00477,119.62996],[-4.0048,119.62996],[-4.00485,119.62995],[-4.00506,119.62992],[-4.00539,119.62988],[-4.00548,119.62987],[-4.00575,119.62983],[-4.00647,119.62974],[-4.00637,119.62907],[-4.00655,119.62905],[-4.00729,119.62897],[-4.00732,119.62897],[-4.00721,119.62796],[-4.00733,119.62792],[-4.0078,119.6279],[-4.00821,119.62785]]
        },
        watang_soreang: {
          name: "Kelurahan Watang Soreang",
          kode: "73.72.03.1003",
          lat: "-3.97864",
          lng: "119.63750",
          coords: [-3.97864, 119.63750],
          desc: "Kantor Kelurahan Watang Soreang, Kecamatan Soreang, Kota Parepare",
          polygon: [[-4.00277,119.6284],[-4.0029,119.62846],[-4.00319,119.62862],[-4.00329,119.6287],[-4.00347,119.62888],[-4.00398,119.6295],[-4.00426,119.62982],[-4.0045,119.63007],[-4.00443,119.63013],[-4.00396,119.63054],[-4.00373,119.63073],[-4.00334,119.63106],[-4.00311,119.63125],[-4.00288,119.63145],[-4.00267,119.63163],[-4.00237,119.63186],[-4.0022,119.63199],[-4.00216,119.63202],[-4.00204,119.63214],[-4.00194,119.63226],[-4.00178,119.63242],[-4.00166,119.63256],[-4.00145,119.63278],[-4.00131,119.63294],[-4.001,119.63327],[-4.00078,119.63354],[-4.00073,119.6336],[-4.00067,119.63367],[-4.00051,119.63384],[-4.00034,119.63398],[-3.99987,119.63431],[-3.99962,119.63448],[-3.99947,119.63459],[-3.99919,119.6348],[-3.99877,119.63511],[-3.99844,119.63534],[-3.99829,119.63545],[-3.99782,119.63585],[-3.99766,119.63594],[-3.9975,119.63603],[-3.9973,119.63612],[-3.99712,119.63618],[-3.99697,119.63624],[-3.99665,119.63639],[-3.99659,119.63641],[-3.99647,119.63646],[-3.99623,119.63656],[-3.99605,119.63661],[-3.9959,119.63665],[-3.99569,119.6367],[-3.9954,119.63676],[-3.99518,119.63681],[-3.99462,119.63692],[-3.99417,119.63702],[-3.99407,119.63704],[-3.99372,119.63712],[-3.99333,119.6372],[-3.99306,119.63726],[-3.9928,119.6373],[-3.99264,119.63729],[-3.99226,119.63725],[-3.99217,119.63724],[-3.99177,119.63724],[-3.99142,119.63723],[-3.99126,119.63725],[-3.99114,119.63727],[-3.99091,119.63732],[-3.99083,119.63735],[-3.99057,119.63743],[-3.99023,119.63755],[-3.99018,119.63757],[-3.9899,119.63765],[-3.98962,119.63772],[-3.98944,119.63774],[-3.98928,119.63774],[-3.98904,119.63771],[-3.98882,119.63768],[-3.98856,119.63765],[-3.98844,119.63763],[-3.98836,119.63759],[-3.98827,119.63754],[-3.98794,119.63732],[-3.98779,119.63722],[-3.98731,119.63689],[-3.98695,119.63663],[-3.98654,119.63635],[-3.98641,119.63626],[-3.98632,119.63621],[-3.98609,119.63607],[-3.98593,119.636],[-3.98582,119.63596],[-3.98518,119.63579],[-3.98502,119.63575],[-3.98486,119.6357],[-3.98457,119.63561],[-3.98442,119.63556],[-3.9843,119.63553],[-3.98411,119.6355],[-3.98383,119.63549],[-3.98366,119.63548],[-3.98337,119.63547],[-3.98323,119.63546],[-3.98273,119.63547],[-3.98244,119.63548],[-3.98231,119.63548],[-3.98235,119.63522],[-3.98249,119.63477],[-3.98262,119.6348],[-3.98282,119.6341],[-3.98293,119.63413],[-3.98302,119.63409],[-3.9831,119.63407],[-3.9832,119.6341],[-3.98328,119.63413],[-3.98346,119.63412],[-3.98353,119.63393],[-3.98445,119.63426],[-3.98535,119.63461],[-3.98547,119.63464],[-3.98582,119.63472],[-3.98617,119.63475],[-3.98636,119.63475],[-3.9869,119.63455],[-3.98732,119.63434],[-3.98737,119.63432],[-3.98752,119.63424],[-3.98792,119.63407],[-3.98842,119.63383],[-3.9894,119.63348],[-3.99,119.63361],[-3.99036,119.63366],[-3.99032,119.63299],[-3.99111,119.63286],[-3.99179,119.63272],[-3.99182,119.63316],[-3.99183,119.63337],[-3.99232,119.63335],[-3.99353,119.63302],[-3.99352,119.63289],[-3.99352,119.63284],[-3.99368,119.63286],[-3.99396,119.63288],[-3.99413,119.63286],[-3.99488,119.63266],[-3.99656,119.63223],[-3.99712,119.63195],[-3.9983,119.6311],[-3.99896,119.63086],[-3.99991,119.63049],[-4.00046,119.63027],[-4.001,119.63004],[-4.00175,119.6296],[-4.00195,119.62941],[-4.00229,119.62901],[-4.00241,119.62884],[-4.00266,119.62852],[-4.00277,119.6284]]
        }
      };

      let currentActivePolygon = null;
      let currentMarker = null;

      // Function to render selected Wilayah (Matches Wilayah ID cahyadsn.com behavior)
      function renderSelectedWilayah(key) {
        const item = kelurahanData[key] || kelurahanData.all;

        // 1. Update Badge & Text Info Elements
        const elBadge = document.getElementById('s3KodeWilayahBadge');
        const elTitle = document.getElementById('s3SelectedKelurahanTitle');
        const elDesc = document.getElementById('s3SelectedKelurahanDesc');
        const elDetail = document.getElementById('s3SelectedKelurahanDetail');
        const elCoord = document.getElementById('s3SelectedKoordinat');
        const elFooter = document.getElementById('s3MapFooterKode');

        // 2. Remove previous active polygon & marker
        if (currentActivePolygon) map.removeLayer(currentActivePolygon);
        if (currentMarker) map.removeLayer(currentMarker);

        // 3. Draw High-Precision Polygon Boundary & Calculate Exact Centroid
        if (item.polygon && item.polygon.length > 0) {
          currentActivePolygon = L.polygon(item.polygon, {
            color: '#0284c7',
            weight: 3.5,
            opacity: 0.9,
            fillColor: '#3b82f6',
            fillOpacity: 0.18,
            lineJoin: 'round'
          }).addTo(map);

          // Calculate exact polygon center point dynamically!
          const bounds = currentActivePolygon.getBounds();
          const centerPoint = bounds.getCenter();
          const latStr = centerPoint.lat.toFixed(5);
          const lngStr = centerPoint.lng.toFixed(5);

          // Update Info text with exact polygon center
          if (elBadge) elBadge.textContent = 'Kode: ' + item.kode;
          if (elTitle) elTitle.textContent = item.name;
          if (elDesc) elDesc.textContent = key === 'all' ? 'Sesuai Kepmendagri No 300.2.2-2430 Tahun 2025' : 'Kelurahan Terintegrasi Kecamatan Soreang Parepare';
          if (elDetail) elDetail.textContent = item.name;
          if (elCoord) elCoord.textContent = latStr + ', ' + lngStr;
          if (elFooter) {
            elFooter.innerHTML = `KODE <strong>${item.kode}</strong> &nbsp; LAT <strong>${latStr}</strong> &nbsp; LNG <strong>${lngStr}</strong>`;
          }

          // 4. Place Single Marker EXACTLY at the center of the active polygon!
          currentMarker = L.marker(centerPoint).addTo(map);
          currentMarker.bindPopup(`<b>${item.name}</b><br>${item.desc}<br><small class="text-primary font-monospace">Kode: ${item.kode} | Lat: ${latStr}, Lng: ${lngStr}</small>`).openPopup();

          map.fitBounds(bounds, { padding: [30, 30] });
        }
      }

      // Initial Render (All Soreang)
      renderSelectedWilayah('all');

      // Dropdown Select Event
      const selectEl = document.getElementById('soreangKelurahanSelect');
      if (selectEl) {
        selectEl.addEventListener('change', function () {
          renderSelectedWilayah(this.value);
        });
      }

    });
  })();
</script>
@endpush
