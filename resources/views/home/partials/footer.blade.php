<footer id="footer" class="footer bg-dark text-white pt-5 pb-3">

  <div class="footer-newsletter border-bottom border-secondary pb-4 mb-4">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-7">
          <h4 class="fw-bold text-white mb-2">Dapatkan Informasi Terbaru Service 3S</h4>
          <p class="text-white-50 small mb-3">Berlangganan buletin resmi layanan kependudukan & informasi kegiatan Kecamatan Soreang, Kota Parepare.</p>
          <form action="#" method="post" class="php-email-form">
            @csrf
            <div class="input-group input-group-lg max-w-lg mx-auto shadow-sm rounded-pill overflow-hidden bg-white p-1">
              <input type="email" name="email" class="form-control border-0 px-3 fs-6" placeholder="Masukkan alamat email Anda" required>
              <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold fs-6">Berlangganan</button>
            </div>
            <div class="loading text-white-50 small mt-2">Memuat...</div>
            <div class="error-message text-danger small mt-2"></div>
            <div class="sent-message text-success small mt-2">Terima kasih! Permintaan berlangganan Anda telah terkirim.</div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container footer-top py-4">
    <div class="row gy-4">
      
      <!-- Column 1: Info Kecamatan -->
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none text-white mb-3">
          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: 700;">
            3S
          </div>
          <span class="sitename fs-5 fw-bold">{{ $siteInfo['name'] ?? 'SOREANG SMART SERVICE' }}</span>
        </a>
        <p class="text-white-50 small mb-3" style="line-height: 1.6;">
          {{ $siteInfo['description'] ?? 'Sistem Pelayanan Publik Terintegrasi Berbasis Digital dan Kolaboratif Kecamatan Soreang Kota Parepare.' }}
        </p>
        <div class="footer-contact pt-2 text-white-50 small">
          <p class="mb-1"><i class="bi bi-geo-alt text-primary me-2"></i>{{ $siteInfo['address_line1'] ?? 'Jl. Jenderal Sudirman No. 45' }}</p>
          <p class="mb-2 ms-4">{{ $siteInfo['address_line2'] ?? 'Kota Parepare, Sulawesi Selatan 91131' }}</p>
          <p class="mb-1"><i class="bi bi-telephone text-primary me-2"></i> Telepon: <span class="text-white">{{ $siteInfo['phone'] ?? '(0421) 21055' }}</span></p>
          <p class="mb-0"><i class="bi bi-envelope text-primary me-2"></i> Email: <span class="text-white">{{ $siteInfo['email'] ?? 'layanan@soreang.parepare.go.id' }}</span></p>
        </div>
      </div>

      <!-- Column 2: Akses Cepat -->
      <div class="col-lg-2 col-md-3 footer-links">
        <h5 class="fw-bold text-white mb-3 fs-6 border-bottom border-primary pb-2 d-inline-block">Akses Cepat</h5>
        <ul class="list-unstyled text-white-50 small">
          <li class="mb-2"><i class="bi bi-chevron-right text-primary me-1"></i> <a href="{{ route('home') }}#hero" class="text-white-50 text-decoration-none hover-white">Beranda</a></li>
          <li class="mb-2"><i class="bi bi-chevron-right text-primary me-1"></i> <a href="{{ route('home') }}#profil" class="text-white-50 text-decoration-none hover-white">Profil Kecamatan</a></li>
          <li class="mb-2"><i class="bi bi-chevron-right text-primary me-1"></i> <a href="{{ route('home') }}#jelajahi" class="text-white-50 text-decoration-none hover-white">Jelajahi Services</a></li>
          <li class="mb-2"><i class="bi bi-chevron-right text-primary me-1"></i> <a href="{{ route('pengaduan.index') }}" class="text-white-50 text-decoration-none hover-white">Pengaduan Warga</a></li>
          <li class="mb-2"><i class="bi bi-chevron-right text-primary me-1"></i> <a href="{{ route('berita.public.index') }}" class="text-white-50 text-decoration-none hover-white">Portal Berita</a></li>
          <li class="mb-2"><i class="bi bi-chevron-right text-primary me-1"></i> <a href="{{ route('home') }}#cek-status" class="text-white-50 text-decoration-none hover-white">Cek Status Tiket</a></li>
          <li class="mb-0"><i class="bi bi-chevron-right text-primary me-1"></i> <a href="{{ route('home') }}#faq" class="text-white-50 text-decoration-none hover-white">FAQ & Bantuan</a></li>
        </ul>
      </div>

      <!-- Column 3: Kelurahan Terintegrasi -->
      <div class="col-lg-3 col-md-3 footer-links">
        <h5 class="fw-bold text-white mb-3 fs-6 border-bottom border-primary pb-2 d-inline-block">Kelurahan Terintegrasi</h5>
        <ul class="list-unstyled text-white-50 small">
          <li class="mb-1"><i class="bi bi-building text-primary me-1"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal" class="text-white-50 text-decoration-none hover-white">Kel. Bukit Harapan</a></li>
          <li class="mb-1"><i class="bi bi-building text-primary me-1"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal" class="text-white-50 text-decoration-none hover-white">Kel. Bukit Indah</a></li>
          <li class="mb-1"><i class="bi bi-building text-primary me-1"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal" class="text-white-50 text-decoration-none hover-white">Kel. Kampung Pisang</a></li>
          <li class="mb-1"><i class="bi bi-building text-primary me-1"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal" class="text-white-50 text-decoration-none hover-white">Kel. Lakessi</a></li>
          <li class="mb-1"><i class="bi bi-building text-primary me-1"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal" class="text-white-50 text-decoration-none hover-white">Kel. Ujung Baru</a></li>
          <li class="mb-1"><i class="bi bi-building text-primary me-1"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal" class="text-white-50 text-decoration-none hover-white">Kel. Ujung Lare</a></li>
          <li class="mb-0"><i class="bi bi-building text-primary me-1"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal" class="text-white-50 text-decoration-none hover-white">Kel. Watang Soreang</a></li>
        </ul>
      </div>

      <!-- Column 4: Social & Help -->
      <div class="col-lg-3 col-md-12">
        <h5 class="fw-bold text-white mb-3 fs-6 border-bottom border-primary pb-2 d-inline-block">Koneksi & Media Sosial</h5>
        <p class="text-white-50 small mb-3">Terhubung dengan kanal informasi digital Kecamatan Soreang Kota Parepare.</p>
        <div class="social-links d-flex gap-2">
          <a href="{{ $siteInfo['social_links']['instagram'] ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-instagram"></i></a>
          <a href="{{ $siteInfo['social_links']['facebook'] ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-facebook"></i></a>
          <a href="{{ $siteInfo['social_links']['youtube'] ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-youtube"></i></a>
          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteInfo['whatsapp'] ?? '6281234567890') }}" target="_blank" class="btn btn-sm btn-success rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>

    </div>
  </div>

  <div class="container text-center pt-3 border-top border-secondary mt-3">
    <p class="text-white-50 small mb-1">© <span>Copyright {{ date('Y') }}</span> <strong class="px-1 text-white">Soreang Smart Service (3S)</strong>. All Rights Reserved</p>
    <div class="credits text-white-50 small" style="font-size: 0.78rem;">
      Pemerintah Kecamatan Soreang • Kota Parepare, Sulawesi Selatan
    </div>
  </div>

</footer>
