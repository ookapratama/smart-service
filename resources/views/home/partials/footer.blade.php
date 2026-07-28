<footer id="footer" class="footer">

  <div class="footer-newsletter">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-6">
          <h4>Dapatkan Informasi Terbaru Service 3S</h4>
          <p>Berlangganan buletin layanan kependudukan dan kegiatan pembangunan Kecamatan Soreang.</p>
          <form action="#" method="post" class="php-email-form">
            @csrf
            <div class="newsletter-form">
              <input type="email" name="email" placeholder="Masukkan email Anda" required>
              <input type="submit" value="Berlangganan">
            </div>
            <div class="loading">Memuat...</div>
            <div class="error-message"></div>
            <div class="sent-message">Permintaan berlangganan Anda telah terkirim. Terima kasih!</div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="{{ route('home') }}" class="d-flex align-items-center">
          <span class="sitename">Soreang Smart Service (3S)</span>
        </a>
        <div class="footer-contact pt-3">
          <p>{{ $siteInfo['address_line1'] ?? 'Jl. Jenderal Sudirman No. 45' }}</p>
          <p>{{ $siteInfo['address_line2'] ?? 'Kota Parepare, Sulawesi Selatan' }}</p>
          <p class="mt-3"><strong>Telepon:</strong> <span>{{ $siteInfo['phone'] ?? '(0421) 21055' }}</span></p>
          <p><strong>Email:</strong> <span>{{ $siteInfo['email'] ?? 'layanan@soreang.parepare.go.id' }}</span></p>
        </div>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Akses Cepat</h4>
        <ul>
          <li><i class="bi bi-chevron-right"></i> <a href="#hero">Beranda</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#fitur">8 Komponen Smart</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#cek-status">Cek Status Tiket</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#berita">Berita & Kegiatan</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#faq">Pertanyaan FAQ</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-3 footer-links">
        <h4>Kelurahan Terintegrasi</h4>
        <ul>
          <li><i class="bi bi-chevron-right"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal">Kelurahan Bukit Harapan</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal">Kelurahan Bukit Indah</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal">Kelurahan Kampung Pisang</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal">Kelurahan Lakessi</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal">Kelurahan Ujung Baru</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal">Kelurahan Ujung Lare</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#jadwal-pelayanan-modal" data-bs-toggle="modal">Kelurahan Watang Soreang</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-12">
        <h4>Media Sosial</h4>
        <p>{{ $siteInfo['about_short'] ?? 'Sistem Pelayanan Publik Terintegrasi Berbasis Digital Kecamatan Soreang.' }}</p>
        <div class="social-links d-flex">
          <a href="{{ $siteInfo['social_links']['instagram'] ?? '#' }}"><i class="bi bi-instagram"></i></a>
          <a href="{{ $siteInfo['social_links']['facebook'] ?? '#' }}"><i class="bi bi-facebook"></i></a>
          <a href="{{ $siteInfo['social_links']['youtube'] ?? '#' }}"><i class="bi bi-youtube"></i></a>
          <a href="https://wa.me/6281234567890"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>

    </div>
  </div>

  <div class="container copyright text-center mt-4">
    <p>© <span>Copyright 2026</span> <strong class="px-1 sitename">Soreang Smart Service (3S)</strong> <span>All Rights Reserved</span></p>
    <div class="credits">
      Pemerintah Kecamatan Soreang
    </div>
  </div>

</footer>
