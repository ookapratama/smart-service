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
  <section id="hero" class="hero section position-relative py-5 overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.88) 0%, rgba(4, 18, 55, 0.92) 100%), url('{{ asset('assets/home/img/soreang-hero.png') }}') center/cover no-repeat !important; min-height: 80vh; display: flex; align-items: center;">
    
    <div class="container position-relative z-2 text-white py-4">
      <div class="row align-items-center gy-5">
        
        <!-- Left Content -->
        <div class="col-lg-7 order-2 order-lg-1" data-aos="fade-right">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm">
            <span class="small fw-semibold text-dark">Portal Resmi Kecamatan Soreang • Kota Parepare</span>
          </div>

          <h1 class="display-4 fw-extrabold mb-3" style="color: #ffffff !important; line-height: 1.15; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">
            Soreang Smart Service (3S)
          </h1>
          
          <p class="fs-5 mb-4 me-lg-4" style="color: #f1f5f9 !important; line-height: 1.65; font-weight: 400; text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);">
            Pelayanan kependudukan digital terpadu, pengaduan publik, dan portal informasi resmi Kecamatan Soreang Kota Parepare secara cepat, mudah, dan transparan.
          </p>

          <div class="d-flex flex-wrap gap-3 align-items-center">
            <a href="#jenis-surat-modal" data-bs-toggle="modal" class="btn btn-light btn-lg rounded-pill px-4 py-3 fw-bold text-primary shadow-lg d-inline-flex align-items-center gap-2 hover-lift">
              <i class="bi bi-file-earmark-plus-fill fs-5"></i>
              <span>Pengajuan Surat Online</span>
            </a>
            <a href="#profil" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 hover-lift">
              <i class="bi bi-info-circle fs-5"></i>
              <span>Profil Kecamatan</span>
            </a>
          </div>
        </div>

        <!-- Right Graphic Photo of Soreang -->
        <div class="col-lg-5 order-1 order-lg-2 text-center" data-aos="zoom-in" data-aos-delay="200">
          <div class="position-relative d-inline-block w-100">
            <div class="p-2 bg-white bg-opacity-15 rounded-4 border border-white border-opacity-30 shadow-2xl backdrop-blur">
              <img src="{{ asset('assets/home/img/soreang-hero.png') }}" class="img-fluid rounded-4 shadow-lg w-100 object-fit-cover" alt="Kantor Kecamatan Soreang Kota Parepare" style="max-height: 360px;">
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
        'title' => 'Profil & Visi Misi Kecamatan Soreang',
        'description' => 'Mengenal profil wilayah Kecamatan Soreang Kota Parepare serta arah komitmen pelayanan publik.'
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
                  <h4 class="fw-bold text-dark m-0">Kecamatan Soreang</h4>
                  <span class="text-primary fw-semibold small">Kota Parepare • Sulawesi Selatan</span>
                </div>
              </div>

              <p class="text-secondary leading-relaxed mb-4" style="font-size: 0.96rem; line-height: 1.7;">
                Kecamatan Soreang merupakan salah satu kawasan pusat pemerintahan dan aktivitas ekonomi masyarakat di Kota Parepare. Terdiri dari <strong>7 Kelurahan</strong>, Kecamatan Soreang mengusung inovasi pelayanan digital <strong>Soreang Smart Service (3S)</strong> untuk memudahkan pengurusan surat, pengaduan publik, serta transparansi data kependudukan.
              </p>
            </div>

            <div class="row g-3 pt-3 border-top">
              <div class="col-6">
                <div class="p-3 bg-white rounded-4 border shadow-sm text-center">
                  <span class="d-block text-muted small fw-semibold mb-1">Kode Wilayah</span>
                  <h4 class="fw-bold text-primary m-0">73.72.03</h4>
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
                <span class="small text-muted">Kecamatan Soreang</span>
              </div>
              <h4 class="fw-bold text-dark mb-2">"Parepare Terkemuka & Soreang Smart Sejahtera"</h4>
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
                <img src="{{ asset('assets/home/img/soreang-service.png') }}" class="img-fluid rounded-4 w-100 object-fit-cover shadow-sm" alt="Pusat Pelayanan Publik Kecamatan Soreang Parepare" style="max-height: 280px;">
              </div>
            </div>

            <div class="p-4 bg-white rounded-4 border shadow-sm position-relative overflow-hidden">
              <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-bold mb-2">Solusi Terintegrasi</span>
              <h4 class="fw-bold text-dark mb-2">Pelayanan Cepat, Transparan, & Tanpa Antri</h4>
              <p class="text-muted small leading-relaxed mb-3">
                Warga Kecamatan Soreang kini dapat mengajukan surat keterangan online, melacak tiket status permohonan secara real-time, dan menyampaikan aspirasi tanpa harus datang mengantri di kantor kelurahan.
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



  <!-- 10. SECTION FAQ ACCORDION -->
  <section id="faq" class="py-5">
    
    @include('home.components.section-title', [
      'subtitle' => 'Faq',
      'title' => 'Pertanyaan Yang Sering Diajukan',
      'description' => 'Informasi jawaban atas pertanyaan seputar layanan Soreang Smart Service.'
    ])

    <div class="container mt-4">
      <div class="row justify-content-center">
        <div class="col-lg-10" data-aos="fade-up">
          <div class="accordion accordion-flush" id="faqAccordion">
            @foreach($faqs as $index => $faq)
              <div class="accordion-item mb-3 border rounded-4 overflow-hidden shadow-sm">
                <h2 class="accordion-header" id="faqHeading{{ $index }}">
                  <button class="accordion-button fw-bold text-dark py-3 px-4 {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                    <i class="bi bi-question-circle-fill text-primary me-3 fs-5"></i> {{ $faq['question'] }}
                  </button>
                </h2>
                <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                  <div class="accordion-body text-secondary px-4 pb-4 pt-0" style="line-height: 1.6; font-size: 0.95rem;">
                    {{ $faq['answer'] }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
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

      // Map initialization
      const soreangCenter = [-3.98924, 119.64297];
      const map = L.map('soreangMap').setView(soreangCenter, 13);

      // OpenStreetMap Tiles
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Wilayah ID 73.72.03'
      }).addTo(map);

      // Data 7 Kelurahan + Data Kecamatan Soreang (Lengkap Kode Wilayah & Polygon Bounds)
      const kelurahanData = {
        all: {
          name: "Kecamatan Soreang",
          kode: "73.72.03",
          lat: "-3.98924",
          lng: "119.64297",
          coords: [-3.98924, 119.64297],
          desc: "Batas Wilayah Administrasi Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-3.9700, 119.6480],
            [-3.9740, 119.6590],
            [-3.9880, 119.6540],
            [-4.0080, 119.6380],
            [-4.0040, 119.6220],
            [-3.9850, 119.6250]
          ]
        },
        bukit_harapan: {
          name: "Kelurahan Bukit Harapan",
          kode: "73.72.03.1007",
          lat: "-3.98226",
          lng: "119.64753",
          coords: [-3.98226, 119.64753],
          desc: "Kantor Kelurahan Bukit Harapan, Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-3.9700, 119.6480],
            [-3.9740, 119.6590],
            [-3.9850, 119.6540],
            [-3.9890, 119.6450],
            [-3.9830, 119.6380],
            [-3.9750, 119.6420]
          ]
        },
        bukit_indah: {
          name: "Kelurahan Bukit Indah",
          kode: "73.72.03.1006",
          lat: "-3.99000",
          lng: "119.63800",
          coords: [-3.99000, 119.63800],
          desc: "Kantor Kelurahan Bukit Indah, Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-3.9850, 119.6380],
            [-3.9890, 119.6450],
            [-3.9950, 119.6420],
            [-3.9940, 119.6340],
            [-3.9870, 119.6330]
          ]
        },
        kampung_pisang: {
          name: "Kelurahan Kampung Pisang",
          kode: "73.72.03.1002",
          lat: "-4.00100",
          lng: "119.62800",
          coords: [-4.00100, 119.62800],
          desc: "Kantor Kelurahan Kampung Pisang, Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-3.9980, 119.6250],
            [-3.9970, 119.6310],
            [-4.0040, 119.6330],
            [-4.0050, 119.6240]
          ]
        },
        lakessi: {
          name: "Kelurahan Lakessi",
          kode: "73.72.03.1001",
          lat: "-3.99350",
          lng: "119.62650",
          coords: [-3.99350, 119.62650],
          desc: "Kantor Kelurahan Lakessi, Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-3.9890, 119.6230],
            [-3.9880, 119.6300],
            [-3.9970, 119.6310],
            [-3.9980, 119.6240]
          ]
        },
        ujung_baru: {
          name: "Kelurahan Ujung Baru",
          kode: "73.72.03.1003",
          lat: "-4.00400",
          lng: "119.63200",
          coords: [-4.00400, 119.63200],
          desc: "Kantor Kelurahan Ujung Baru, Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-4.0010, 119.6300],
            [-4.0000, 119.6360],
            [-4.0080, 119.6380],
            [-4.0070, 119.6290]
          ]
        },
        ujung_lare: {
          name: "Kelurahan Ujung Lare",
          kode: "73.72.03.1004",
          lat: "-3.99600",
          lng: "119.63600",
          coords: [-3.99600, 119.63600],
          desc: "Kantor Kelurahan Ujung Lare, Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-3.9930, 119.6330],
            [-3.9920, 119.6400],
            [-4.0000, 119.6410],
            [-4.0010, 119.6330]
          ]
        },
        watang_soreang: {
          name: "Kelurahan Watang Soreang",
          kode: "73.72.03.1005",
          lat: "-3.98500",
          lng: "119.63200",
          coords: [-3.98500, 119.63200],
          desc: "Kantor Kelurahan Watang Soreang, Kecamatan Soreang, Kota Parepare",
          polygon: [
            [-3.9800, 119.6270],
            [-3.9790, 119.6360],
            [-3.9880, 119.6370],
            [-3.9890, 119.6280]
          ]
        }
      };

      const markers = {};
      let currentActivePolygon = null;

      // Add Markers to map for all 7 kelurahan
      Object.keys(kelurahanData).forEach(key => {
        if (key === 'all') return;
        const item = kelurahanData[key];
        const marker = L.marker(item.coords).addTo(map);
        marker.bindPopup(`<b>${item.name}</b><br>${item.desc}<br><small class="text-primary font-monospace">Kode: ${item.kode} | Lat: ${item.lat}, Lng: ${item.lng}</small>`);
        
        // Clicking marker updates dropdown & polygon
        marker.on('click', function () {
          const selectEl = document.getElementById('soreangKelurahanSelect');
          if (selectEl) selectEl.value = key;
          renderSelectedWilayah(key);
        });

        markers[key] = marker;
      });

      // Function to render selected Wilayah (Update Polygon Line, Info Cards, & Map Focus)
      function renderSelectedWilayah(key) {
        const item = kelurahanData[key] || kelurahanData.all;

        // 1. Update Badge & Text Elements
        const elBadge = document.getElementById('s3KodeWilayahBadge');
        const elTitle = document.getElementById('s3SelectedKelurahanTitle');
        const elDesc = document.getElementById('s3SelectedKelurahanDesc');
        const elDetail = document.getElementById('s3SelectedKelurahanDetail');
        const elCoord = document.getElementById('s3SelectedKoordinat');
        const elFooter = document.getElementById('s3MapFooterKode');

        if (elBadge) elBadge.textContent = 'Kode: ' + item.kode;
        if (elTitle) elTitle.textContent = item.name;
        if (elDesc) elDesc.textContent = key === 'all' ? 'Sesuai Kepmendagri No 300.2.2-2430 Tahun 2025' : 'Kelurahan Terintegrasi Kecamatan Soreang Parepare';
        if (elDetail) elDetail.textContent = item.name;
        if (elCoord) elCoord.textContent = item.lat + ', ' + item.lng;
        if (elFooter) {
          elFooter.innerHTML = `KODE <strong>${item.kode}</strong> &nbsp; LAT <strong>${item.lat}</strong> &nbsp; LNG <strong>${item.lng}</strong>`;
        }

        // 2. Remove previous polygon line
        if (currentActivePolygon) {
          map.removeLayer(currentActivePolygon);
        }

        // 3. Draw updated polygon boundary line (Blue outline with translucent fill as in screenshot)
        currentActivePolygon = L.polygon(item.polygon, {
          color: '#0d6efd',
          weight: 4,
          opacity: 0.9,
          fillColor: '#0d6efd',
          fillOpacity: 0.2,
          lineJoin: 'round'
        }).addTo(map);

        currentActivePolygon.bindPopup(`<b>${item.name}</b><br>Kode Wilayah: ${item.kode}<br><small class="text-primary font-monospace">Lat: ${item.lat}, Lng: ${item.lng}</small>`);

        // 4. Fit map to polygon bounds smoothly
        map.fitBounds(currentActivePolygon.getBounds(), { padding: [35, 35] });

        // 5. Open marker popup if specific kelurahan selected
        if (key !== 'all' && markers[key]) {
          markers[key].openPopup();
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
