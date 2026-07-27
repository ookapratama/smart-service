@extends('home.layouts.app')

@section('title', 'Layanan Pengaduan Masyarakat - Sorean Smart Service (3S)')
@section('meta_description', 'Portal Pengaduan, Aspirasi, dan Saran Masyarakat Kecamatan Sorean. Cepat, Terintegrasi, dan Terjamin Kerahasiaannya.')

@section('content')

  <!-- 1. HERO PENGADUAN BANNER -->
  <section class="hero section light-background py-5" style="background: linear-gradient(135deg, #eef2ff 0%, #f0f7ff 100%);">
    <div class="container py-4">
      <div class="row align-items-center gy-4">
        <div class="col-lg-7" data-aos="fade-up">
          <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill mb-3">
            <i class="bi bi-shield-check me-1"></i> Layanan Pengaduan Resmi 3S
          </span>
          <h1 class="display-5 fw-bold text-dark mb-3">
            Sampaikan Keluhan & Aspirasi Anda Secara Digital
          </h1>
          <p class="lead text-muted mb-4">
            Pemerintah Kecamatan Sorean menyediakan sarana pengaduan publik yang transparan, cepat ditindaklanjuti, dan terjamin kerahasiaan pelapor.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('pengaduan.create') }}" class="btn btn-primary btn-md px-4 py-3 shadow-sm rounded-3 fw-bold">
              <i class="bi bi-pencil-square me-2"></i> Buat Pengaduan Sekarang
            </a>
            <a href="#alur" class="btn btn-outline-primary btn-md px-4 py-3 rounded-3">
              <i class="bi bi-info-circle me-1"></i> Pelajari Alur Pengaduan
            </a>
          </div>
        </div>

        <div class="col-lg-5 text-center" data-aos="fade-up" data-aos-delay="200">
          <div class="bg-white p-4 rounded-4 shadow-lg border border-light">
            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
              <i class="bi bi-megaphone fs-1"></i>
            </div>
            <h4 class="fw-bold mb-2">Pusat Bantuan Pengaduan</h4>
            <p class="text-muted small mb-4">Lacak progres atau kirim pengaduan publik hanya dalam 3 menit.</p>

            <div class="p-3 bg-light rounded-3 text-start mb-3 border">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted">Total Laporan Masuk:</span>
                <span class="fw-bold text-primary fs-5">{{ $totalPengaduan ?? 0 }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <span class="small text-muted">Laporan Selesai:</span>
                <span class="fw-bold text-success fs-5">{{ $pengaduanSelesai ?? 0 }}</span>
              </div>
            </div>

            <a href="{{ route('home') }}#cek-status" class="btn btn-sm btn-outline-secondary w-100">
              <i class="bi bi-search me-1"></i> Cek Status Nomor Tiket
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. SECTION KEUNGGULAN LAYANAN PENGADUAN -->
  <section class="section">
    <div class="container" data-aos="fade-up">
      @include('home.components.section-title', [
        'subtitle' => 'Keunggulan Sistem',
        'title' => 'Mengapa Melapor Melalui Portal 3S?',
        'description' => 'Sistem Pengaduan Terintegrasi dengan fitur perlindungan pelapor & verifikasi otomatis.'
      ])

      <div class="row gy-4">
        <div class="col-md-3">
          <div class="p-4 bg-white border rounded-4 text-center h-100 shadow-sm">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
              <i class="bi bi-shield-lock fs-4"></i>
            </div>
            <h5 class="fw-bold">Privasi Dilindungi</h5>
            <p class="text-muted small mb-0">Tersedia opsi Laporan Anonim / Rahasia sehingga identitas Anda terjaga penuh.</p>
          </div>
        </div>

        <div class="col-md-3">
          <div class="p-4 bg-white border rounded-4 text-center h-100 shadow-sm">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
              <i class="bi bi-lightning-charge fs-4"></i>
            </div>
            <h5 class="fw-bold">Respon Cepat</h5>
            <p class="text-muted small mb-0">Laporan langsung diteruskan ke petugas kelurahan & instansi yang berwenang.</p>
          </div>
        </div>

        <div class="col-md-3">
          <div class="p-4 bg-white border rounded-4 text-center h-100 shadow-sm">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
              <i class="bi bi-upc-scan fs-4"></i>
            </div>
            <h5 class="fw-bold">Nomor Tiket Unik</h5>
            <p class="text-muted small mb-0">Setiap laporan menerbitkan nomor tiket resmi untuk pelacakan status secara live.</p>
          </div>
        </div>

        <div class="col-md-3">
          <div class="p-4 bg-white border rounded-4 text-center h-100 shadow-sm">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
              <i class="bi bi-patch-check fs-4"></i>
            </div>
            <h5 class="fw-bold">Transparan</h5>
            <p class="text-muted small mb-0">Riwayat status & penjelasan tindak lanjut petugas dapat dipantau terbuka.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. SECTION ALUR PENGADUAN -->
  <section id="alur" class="section light-background">
    <div class="container" data-aos="fade-up">
      @include('home.components.section-title', [
        'subtitle' => 'Alur Kerja',
        'title' => '4 Langkah Mudah Menyampaikan Pengaduan',
        'description' => 'Proses sederhana dari pengiriman laporan hingga penyelesaian oleh tim pengaduan 3S.'
      ])

      <div class="row g-4">
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 p-3 text-center rounded-4">
            <span class="badge bg-primary rounded-circle mx-auto my-3 fs-5" style="width: 45px; height: 45px; line-height: 30px;">1</span>
            <h5 class="fw-bold">Isi Form Pengaduan</h5>
            <p class="text-muted small mb-0">Lengkapi identitas, kategori, uraian kronologi, serta lampirkan foto bukti pendukung.</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 p-3 text-center rounded-4">
            <span class="badge bg-primary rounded-circle mx-auto my-3 fs-5" style="width: 45px; height: 45px; line-height: 30px;">2</span>
            <h5 class="fw-bold">Terbit Nomor Tiket</h5>
            <p class="text-muted small mb-0">Sistem menerbitkan nomor tiket resmi (contoh: <code>SRG-2607-XXXXX</code>) sebagai bukti tanda terima.</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 p-3 text-center rounded-4">
            <span class="badge bg-primary rounded-circle mx-auto my-3 fs-5" style="width: 45px; height: 45px; line-height: 30px;">3</span>
            <h5 class="fw-bold">Tindak Lanjut Petugas</h5>
            <p class="text-muted small mb-0">Tim berwenang melakukan tindakan di lapangan dan memverifikasi kebenaran aduan.</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 p-3 text-center rounded-4">
            <span class="badge bg-primary rounded-circle mx-auto my-3 fs-5" style="width: 45px; height: 45px; line-height: 30px;">4</span>
            <h5 class="fw-bold">Laporan Selesai</h5>
            <p class="text-muted small mb-0">Status laporan diperbarui menjadi SELESAI dilengkapi catatan dan dokumentasi penanganan.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. CTA BANNER MASUK FORM -->
  <section class="section py-5 bg-primary text-white">
    <div class="container text-center" data-aos="fade-up">
      <h2 class="fw-bold mb-3 text-white">Siap Menyampaikan Laporan Atau Aspirasi Anda?</h2>
      <p class="lead mb-4 text-white-50 mx-auto" style="max-width: 700px;">
        Klik tombol di bawah ini untuk masuk ke Formulir Pengaduan Digital Kecamatan Sorean.
      </p>
      <a href="{{ route('pengaduan.create') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold text-primary shadow">
        <i class="bi bi-file-earmark-plus me-2"></i> Masuk Ke Form Pengaduan
      </a>
    </div>
  </section>

@endsection
