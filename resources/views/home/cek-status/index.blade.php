@extends('home.layouts.app')

@section('title', 'Cek Status Permohonan & Tiket - Kecamatan Soreang Kota Parepare')
@section('meta_description', 'Lacak status permohonan surat keterangan online dan pengaduan publik Kecamatan Soreang Kota Parepare secara real-time.')

@push('styles')
<style>
  .hover-lift {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08) !important;
  }
  #s3TicketResultBox i,
  #s3TicketResultBox span.bi {
    background: none !important;
    width: auto !important;
    height: auto !important;
    border-radius: 0 !important;
    display: inline-block !important;
  }
  /* Override Template CSS White Overlay */
  #hero-cek-status::before {
    display: none !important;
  }
</style>
@endpush

@section('content')

  <!-- 1. HERO BANNER CEK STATUS -->
  <section id="hero-cek-status" class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.88) 0%, rgba(4, 18, 55, 0.92) 100%), url('{{ !empty($siteInfo['hero_bg']) ? asset('storage/' . $siteInfo['hero_bg']) : asset('assets/home/img/soreang-hero.png') }}') center/cover no-repeat !important; min-height: 48vh; display: flex; align-items: center;">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center text-center">
        <div class="col-lg-9" data-aos="fade-up">
          
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm mx-auto">
            <i class="bi bi-search text-dark"></i>
            <span class="small fw-semibold text-dark">Layanan Mandiri Digital • Kecamatan Soreang</span>
          </div>

          <h1 class="display-5 fw-extrabold mb-3" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">
            Cek Status Permohonan & Tiket
          </h1>
          
          <p class="fs-5 mb-4 px-lg-5" style="color: #f1f5f9 !important; line-height: 1.6; font-weight: 400; text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);">
            Lacak progres surat permohonan atau pengaduan publik Anda secara transparan dan real-time cukup dengan memasukkan Nomor Tiket atau NIK.
          </p>

        </div>
      </div>
    </div>
  </section>

  <!-- 2. MAIN SEARCH & RESULT SECTION -->
  <section class="py-5 bg-light">
    <div class="container py-3">
      
      <!-- Input Card Box -->
      <div class="row justify-content-center" data-aos="fade-up">
        <div class="col-lg-9">
          <div class="p-4 p-md-5 bg-white rounded-4 shadow-sm border mb-5">
            <form id="s3FormCekStatus" class="w-100">
              <label for="s3InputKeyword" class="form-label fw-bold text-dark fs-5 mb-2">
                <i class="bi bi-ticket-perforated text-primary me-2"></i> Nomor Tiket atau NIK Pemohon
              </label>
              <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border p-1 mb-3">
                <span class="input-group-text bg-white border-0 ps-3 text-muted"><i class="bi bi-search fs-5"></i></span>
                <input type="text" id="s3InputKeyword" value="{{ $keyword }}" class="form-control border-0 fs-6 px-2 text-dark" placeholder="Contoh: SRG-2607-00123 atau 737203xxxxxxxxxx" required autocomplete="off">
                <button type="submit" id="s3BtnCekStatus" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-white fs-6 hover-lift">
                  <span>Cari Tiket</span>
                </button>
              </div>
              <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 text-muted small px-2">
                <span><i class="bi bi-info-circle me-1 text-primary"></i> Lacak tanpa harus datang ke kantor kelurahan.</span>
                <button type="button" class="btn btn-link text-decoration-none p-0 small text-primary fw-semibold" onclick="s3TriggerQrScan()">
                  <i class="bi bi-qr-code-scan me-1"></i> Scan QR Code Tiket
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Quick Action Cards Row -->
      <div class="row gy-4 align-items-stretch" data-aos="fade-up" data-aos-delay="100">

        <!-- Card 1: Pelayanan Kelurahan -->
        <div class="col-lg-4 col-md-6">
          <div class="p-4 bg-white rounded-4 shadow-sm border h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3" style="width: 54px; height: 54px; font-size: 1.4rem;">
                <i class="bi bi-clock-history"></i>
              </div>
              <h6 class="fw-bold text-dark mb-1">Jadwal Pelayanan</h6>
              <p class="text-muted small mb-0">Cek jam operasional & kontak petugas PJ pelayanan di 7 kelurahan se-Kecamatan Soreang.</p>
            </div>
            <div class="mt-4">
              <button type="button" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-semibold py-2" data-bs-toggle="modal" data-bs-target="#jadwal-pelayanan-modal">
                <i class="bi bi-calendar-event me-1"></i> Lihat Jadwal Kelurahan
              </button>
            </div>
          </div>
        </div>

        <!-- Card 2: Pengaduan Terpadu -->
        <div class="col-lg-4 col-md-6">
          <div class="p-4 bg-white rounded-4 shadow-sm border h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3" style="width: 54px; height: 54px; font-size: 1.4rem;">
                <i class="bi bi-chat-left-text-fill"></i>
              </div>
              <h6 class="fw-bold text-dark mb-1">Pengaduan Publik</h6>
              <p class="text-muted small mb-0">Sampaikan aspirasi, kendala pelayanan, atau keluhan lingkungan secara online.</p>
            </div>
            <div class="mt-4">
              <a href="{{ route('pengaduan.create') }}" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-semibold py-2">
                <i class="bi bi-plus-circle me-1"></i> Buat Pengaduan Baru
              </a>
            </div>
          </div>
        </div>

        <!-- Card 3: QR Scanner Widget -->
        <div class="col-lg-4 col-md-12">
          <div class="p-4 bg-white rounded-4 shadow-sm border h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3" style="width: 54px; height: 54px; font-size: 1.4rem;">
                <i class="bi bi-qr-code-scan"></i>
              </div>
              <h6 class="fw-bold text-dark mb-1">Pindai Kode QR</h6>
              <p class="text-muted small mb-0">Arahkan kamera smartphone ke QR Code bukti permohonan untuk cek otomatis.</p>
            </div>
            <div class="mt-4">
              <button type="button" class="btn btn-primary btn-sm rounded-pill w-100 fw-semibold py-2" onclick="s3TriggerQrScan()">
                <i class="bi bi-camera me-1"></i> Buka Scanner QR
              </button>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>

  {{-- Include Interactive Modals --}}
  @include('home.partials.modals')

@endsection

@push('scripts')
<script>
  // Auto search if query parameter exists
  document.addEventListener('DOMContentLoaded', function() {
    const keywordInput = document.getElementById('s3InputKeyword');
    if (keywordInput && keywordInput.value.trim().length >= 3) {
      document.getElementById('s3FormCekStatus').dispatchEvent(new Event('submit'));
    }
  });
</script>
@endpush
