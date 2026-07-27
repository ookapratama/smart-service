@extends('home.layouts.app')

@section('title', 'SOREAN SMART SERVICE (3S) - Pelayanan Publik Terintegrasi')
@section('meta_description', 'Sistem Pelayanan Publik Terintegrasi Berbasis Digital Kecamatan Sorean. Cepat, Mudah, Transparan, dan Melayani dengan Hati.')

@push('styles')
<style>
  #s3TicketResultBox i,
  #s3TicketResultBox span.bi {
    background: none !important;
    width: auto !important;
    height: auto !important;
    border-radius: 0 !important;
    display: inline-block !important;
  }
  .modal-header .modal-title,
  .modal-header .modal-title i,
  .modal-header .modal-title span {
    color: #ffffff !important;
    background: none !important;
    width: auto !important;
    height: auto !important;
  }
</style>
@endpush

@section('content')

  <!-- 1. HERO SECTION (BANNER UTAMA) -->
  <section id="hero" class="hero section light-background">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
          <h1>Sorean Smart Service <span>(3S)</span></h1>
          <p>{{ $siteInfo['tagline'] ?? 'Cepat, Mudah, Transparan, dan Melayani dengan Hati.' }}</p>
          <div class="d-flex">
            <a href="{{ route('login') }}" class="btn-get-started">Mulai Sekarang</a>
            <a href="#services" class="btn-watch-video d-flex align-items-center">
              <i class="bi bi-info-circle"></i><span>Pelajari Lebih Lanjut</span>
            </a>
          </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2 hero-img d-flex justify-content-center" data-aos="zoom-out" data-aos-delay="200">
          <img src="{{ asset('assets/home/img/hero-img.png') }}" class="img-fluid animated" alt="Sorean Smart Service Illustration" style="max-height: 380px;">
        </div>
      </div>
    </div>
  </section>

  <!-- 2. SECTION MASALAH & SOLUSI (WHY 3S NEEDED?) -->
  <section id="masalah-solusi" class="about section light-background">
    @include('home.components.section-title', [
      'subtitle' => 'Tantangan & Solusi',
      'title' => 'Mengapa Sorean Smart Service Dihadirkan?',
      'description' => 'Transformasi digital pelayanan publik Kecamatan Sorean untuk mengatasi kendala pelayanan kependudukan konvensional.'
    ])

    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <div class="about-content">
            <h3>Solusi Terintegrasi Pelayanan Publik Digital</h3>
            <p class="fst-italic">
              Sistem Pelayanan Publik Terintegrasi Berbasis Digital dan Kolaboratif Kecamatan Sorean menghadirkan kemudahan pengurusan administrasi warga.
            </p>
            <ul>
              @foreach($challenges as $c)
                <li>
                  <i class="bi {{ $c['icon'] }}"></i>
                  <div>
                    <h4>{{ $c['title'] }}</h4>
                    <p>{{ $c['description'] }}</p>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
          <div class="content ps-0 ps-lg-4">
            <div class="p-4 bg-white rounded-3 shadow-sm border mb-4">
              <h4 class="fw-bold text-primary mb-2"> 3S Menghadirkan Solusi Digital</h4>
              <p class="text-muted small mb-0">Menghubungkan seluruh kelurahan, mempersingkat alur birokrasi, dan memberikan kepastian status permohonan secara real-time.</p>
            </div>
            <img src="{{ asset('assets/home/img/about.jpg') }}" class="img-fluid rounded-4 shadow-sm" alt="Solusi 3S">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. SECTION FITUR UTAMA (8 SMART COMPONENTS) -->
  <section id="services" class="services section">
    @include('home.components.section-title', [
      'subtitle' => '8 Komponen Smart',
      'title' => 'Fitur Utama Sorean Smart Service',
      'description' => 'Modularitas layanan publik berbasis digital yang memudahkan warga dan aparatur kelurahan.'
    ])

    <div class="container">
      <div class="row gy-4">
        @foreach($smartComponents as $index => $comp)
          <div class="col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
            <div class="service-item position-relative text-start p-4 h-100">
              <div class="d-flex align-items-center mb-3">
                <div class="icon me-3 m-0">
                  <i class="bi {{ $comp['bs_icon'] }}"></i>
                </div>
                <div>
                  <h3 class="m-0 fs-5">{{ $comp['title'] }}</h3>
                  <span class="badge bg-light text-primary border me-1">{{ $comp['badge'] }}</span>
                </div>
              </div>
              <p class="mb-3">{{ $comp['description'] }}</p>
              @if($comp['link'] === '#jenis-surat-modal')
                <a href="#jenis-surat-modal" data-bs-toggle="modal" class="fw-bold text-primary text-decoration-none">
                  {{ $comp['link_text'] }} <i class="bi bi-arrow-right"></i>
                </a>
              @elseif($comp['link'] === '#cek-status')
                <a href="#cek-status" class="fw-bold text-primary text-decoration-none">
                  {{ $comp['link_text'] }} <i class="bi bi-arrow-right"></i>
                </a>
              @else
                <a href="{{ $comp['link'] }}" class="fw-bold text-primary text-decoration-none">
                  {{ $comp['link_text'] }} <i class="bi bi-arrow-right"></i>
                </a>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- 4. SECTION KEUNGGULAN (ADVANTAGES) -->
  <section id="keunggulan" class="featured-services section light-background">
    @include('home.components.section-title', [
      'subtitle' => 'Keunggulan',
      'title' => 'Keunggulan System 3S',
      'description' => 'Standar pelayanan publik transparan, akuntabel, dan berbasis data real-time.'
    ])

    <div class="container">
      <div class="row gy-4">
        @foreach($advantages as $index => $adv)
          <div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
            <div class="service-item position-relative w-100">
              <div class="icon"><i class="bi {{ $adv['icon'] }}"></i></div>
              <h4><a href="#" class="stretched-link">{{ $adv['title'] }}</a></h4>
              <p>{{ $adv['description'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- 5. SECTION INDIKATOR KEBERHASILAN -->
  <section id="indikator" class="stats section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4">
        @foreach($metrics as $m)
          <div class="col-lg-4 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi {{ $m['icon'] }} color-blue flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="{{ (float)$m['count'] }}" data-purecounter-duration="1" class="purecounter"></span>
                <p><strong>{{ $m['label'] }}</strong><br><small class="text-muted">{{ $m['description'] }}</small></p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- 6. SECTION GALERI / SCREENSHOT SISTEM -->
  <section id="galeri" class="services section light-background">
    @include('home.components.section-title', [
      'subtitle' => 'Antarmuka',
      'title' => 'Galeri & Screenshot Fitur 3S',
      'description' => 'Tampilan antarmuka sistem yang bersih, cepat, dan mudah digunakan.'
    ])

    <div class="container">
      <div class="row gy-4">
        @foreach($screenshots as $index => $shot)
          <div class="col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
            <div class="service-item text-start p-4 bg-white border rounded-3 h-100">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge bg-primary">{{ $shot['tag'] }}</span>
                <small class="text-muted font-monospace">Modul {{ $index + 1 }}</small>
              </div>
              <h3 class="fs-5 fw-bold mb-2">{{ $shot['title'] }}</h3>
              <p class="text-muted mb-0">{{ $shot['description'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- 7. SECTION BERITA & KEGIATAN -->
  <section id="berita" class="services section">
    @include('home.components.section-title', [
      'subtitle' => 'Publikasi',
      'title' => 'Berita & Kegiatan Terbaru',
      'description' => 'Pembaruan informasi dan sosialisasi pelayanan publik Kecamatan Sorean.'
    ])

    <div class="container">
      <div class="row gy-4">
        @foreach($beritaList as $index => $b)
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
            <div class="card border shadow-sm h-100">
              @php
                $imgPath = $b->thumbnail ?? $b->gambar;
                if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                    $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                }
              @endphp
              @if(!empty($imgPath))
                <img src="{{ $imgPath }}" class="card-img-top" alt="{{ $b->judul }}" style="height: 180px; object-fit: cover;">
              @endif
              <div class="card-body p-4 d-flex flex-column">
                <span class="badge bg-light text-primary border align-self-start mb-2">{{ $b->kategori ?? 'Umum' }}</span>
                <h5 class="card-title fw-bold fs-6 mb-2">{{ $b->judul }}</h5>
                <p class="card-text text-muted small flex-grow-1">{{ Str::limit(strip_tags($b->ringkasan ?? $b->konten ?? ''), 120) }}</p>
                <div class="pt-3 border-top mt-auto text-muted small d-flex justify-content-between">
                  <span><i class="bi bi-calendar-event me-1"></i> {{ is_object($b->created_at) ? $b->created_at->format('d M Y') : date('d M Y') }}</span>
                  <span><i class="bi bi-person me-1"></i> {{ $b->penulis ?? 'Admin' }}</span>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- 8. SECTION QUICK ACCESS / CTA UTAMA -->
  <section id="cek-status" class="contact section light-background">
    @include('home.components.section-title', [
      'subtitle' => 'Layanan Mandiri',
      'title' => 'Cek Status & Akses Cepat',
      'description' => 'Periksa progres surat permohonan Anda, jadwal pelayanan, atau scan QR code tiket secara langsung.'
    ])

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4 align-items-stretch">

        <!-- Box 1: Cek Status -->
        <div class="col-lg-4 col-md-6">
          <div class="info-item d-flex flex-column justify-content-between h-100 p-4 bg-white rounded-3 shadow-sm border" data-aos="fade-up" data-aos-delay="200">
            <div class="text-center">
              <i class="bi bi-search d-flex align-items-center justify-content-center mx-auto mb-3"></i>
              <h3>Cek Status Permohonan</h3>
              <p class="text-muted small mb-0">Masukkan NIK atau Nomor Tiket permohonan Anda untuk mengecek progres live.</p>
            </div>

            <div class="w-100 mt-4">
              <form id="s3FormCekStatus" class="w-100">
                <div class="mb-3">
                  <input type="text" id="s3InputKeyword" class="form-control" placeholder="SRG-2607-00123 atau NIK" required>
                </div>
                <button type="submit" id="s3BtnCekStatus" class="btn btn-primary w-100">
                   Cek Status
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Box 2: Jadwal Pelayanan -->
        <div class="col-lg-4 col-md-6">
          <div class="info-item d-flex flex-column justify-content-between h-100 p-4 bg-white rounded-3 shadow-sm border" data-aos="fade-up" data-aos-delay="300">
            <div class="text-center">
              <i class="bi bi-clock d-flex align-items-center justify-content-center mx-auto mb-3"></i>
              <h3>Jadwal Pelayanan</h3>
              <p class="text-muted small mb-0">Jadwal operasional kantor kelurahan & jam kerja pelayanan publik se-Kecamatan Sorean.</p>
            </div>

            <div class="w-100 mt-4">
              <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#jadwal-pelayanan-modal">
                Lihat Jadwal Kelurahan
              </button>
            </div>
          </div>
        </div>

        <!-- Box 3: Scan QR Code -->
        <div class="col-lg-4 col-md-12">
          <div class="info-item d-flex flex-column justify-content-between h-100 p-4 bg-white rounded-3 shadow-sm border" data-aos="fade-up" data-aos-delay="400">
            <div class="text-center">
              <i class="bi bi-qr-code-scan d-flex align-items-center justify-content-center mx-auto mb-3"></i>
              <h3>Scan QR Code Tiket</h3>
              <p class="text-muted small mb-0">Pindai QR Code yang tercetak pada resi tiket permohonan untuk verifikasi instan.</p>
            </div>

            <div class="w-100 mt-4">
              <button type="button" class="btn btn-primary w-100" onclick="s3TriggerQrScan()">
                Buka QR Scanner
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 9. SECTION FAQ ACCORDION -->
  <section id="faq" class="faq section">
    @include('home.components.section-title', [
      'subtitle' => 'Faq',
      'title' => 'Pertanyaan Yang Sering Diajukan',
      'description' => 'Informasi jawaban atas pertanyaan seputar layanan Sorean Smart Service.'
    ])

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
          <div class="accordion" id="faqAccordion">
            @foreach($faqs as $index => $faq)
              <div class="accordion-item mb-3 border rounded-3 overflow-hidden">
                <h2 class="accordion-header" id="faqHeading{{ $index }}">
                  <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                    <i class="bi bi-question-circle me-2 text-primary"></i> {{ $faq['question'] }}
                  </button>
                </h2>
                <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
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

  <!-- MODALS -->

  <!-- Modal 1: Jenis Surat List -->
  <div class="modal fade" id="jenis-surat-modal" tabindex="-1" aria-labelledby="jenisSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold text-white" id="jenisSuratModalLabel">
            <i class="bi bi-file-earmark-text me-2 text-white"></i> Daftar Jenis Surat Keterangan Online 3S
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted small mb-3">Berikut 12 jenis surat administrasi kependudukan yang dapat diajukan secara digital via 3S:</p>
          <div class="row g-3">
            @foreach($jenisSuratList as $js)
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-primary">{{ $js->kode }}</span>
                    @if($js->wajib_pengantar_rt_rw)
                      <small class="text-warning fw-bold"><i class="bi bi-exclamation-circle me-1"></i> Wajib RT/RW</small>
                    @else
                      <small class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i> Langsung Proses</small>
                    @endif
                  </div>
                  <h6 class="fw-bold text-dark mb-1">{{ $js->nama }}</h6>
                  <p class="text-muted small mb-0">{{ $js->deskripsi }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <a href="{{ route('login') }}" class="btn btn-primary">Pengajuan Sekarang <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 2: Jadwal Pelayanan per Kelurahan -->
  <div class="modal fade" id="jadwal-pelayanan-modal" tabindex="-1" aria-labelledby="jadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold text-white" id="jadwalModalLabel">
            <i class="bi bi-clock-history me-2 text-white"></i> Jadwal Pelayanan Kelurahan Se-Kecamatan Sorean
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <input type="text" id="s3SearchJadwal" class="form-control" placeholder="Cari nama kelurahan (contoh: Mekarjaya)...">
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Kelurahan</th>
                  <th>Jam Operasional</th>
                  <th>Istirahat</th>
                  <th>Petugas Penanggung Jawab</th>
                  <th>Telepon</th>
                </tr>
              </thead>
              <tbody id="s3JadwalTableBody">
                @foreach($jadwalList as $j)
                  <tr>
                    <td class="fw-bold text-primary">{{ $j->kelurahan }}</td>
                    <td><span class="badge bg-success">{{ $j->jam_buka }} - {{ $j->jam_tutup }}</span></td>
                    <td><small class="text-muted">{{ $j->istirahat ?? '-' }}</small></td>
                    <td><small class="fw-semibold">{{ $j->petugas ?? '-' }}</small></td>
                    <td><small class="text-muted">{{ $j->telepon ?? '-' }}</small></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 3: QR Scanner Simulator -->
  <div class="modal fade" id="s3QrScannerModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold text-white" id="qrModalLabel">
            <i class="bi bi-qr-code-scan me-2 text-white"></i> QR Code Scanner 3S
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="p-4 bg-light border border-2 border-dashed rounded-3 mb-3 position-relative">
            <i class="bi bi-camera fs-1 text-muted d-block mb-2"></i>
            <p class="text-muted small mb-0">Arahkan kamera ke QR Code Tiket 3S</p>
          </div>
          <p class="small text-muted mb-2">Atau gunakan sampel tiket berikut untuk pengujian:</p>
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="s3SimulateQrInput('SRG-2607-00123')">SRG-2607-00123</button>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="s3SimulateQrInput('3204012345670001')">NIK 3204012345670001</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 4: Pop Up Cek Status Result -->
  <div class="modal fade" id="modalCekStatusResult" tabindex="-1" aria-labelledby="modalCekStatusResultLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="modal-header bg-primary text-white py-3">
          <h5 class="modal-title fw-bold text-white mb-0" id="modalCekStatusResultLabel">
            <i class="bi bi-ticket-perforated me-2 text-white"></i> Hasil Cek Status Permohonan
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" id="s3TicketModalBody">
          <!-- Content will be injected dynamically via JS -->
        </div>
        <div class="modal-footer bg-light border-0 py-2">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

@endsection
