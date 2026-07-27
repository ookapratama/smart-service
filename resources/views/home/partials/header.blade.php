<header id="header" class="header sticky-top">

  <!-- Top Bar -->
  <div class="topbar d-flex align-items-center">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-envelope d-flex align-items-center">
          <a href="mailto:{{ $siteInfo['email'] ?? 'layanan@sorean.go.id' }}">{{ $siteInfo['email'] ?? 'layanan@sorean.go.id' }}</a>
        </i>
        <i class="bi bi-phone d-flex align-items-center ms-4">
          <span>{{ $siteInfo['phone'] ?? '(0251) 833-3373' }}</span>
        </i>
      </div>
      <div class="social-links d-none d-md-flex align-items-center">
        @if(!empty($siteInfo['social_links']['instagram']))
          <a href="{{ $siteInfo['social_links']['instagram'] }}" target="_blank" class="instagram"><i class="bi bi-instagram"></i></a>
        @endif
        @if(!empty($siteInfo['social_links']['facebook']))
          <a href="{{ $siteInfo['social_links']['facebook'] }}" target="_blank" class="facebook"><i class="bi bi-facebook"></i></a>
        @endif
        @if(!empty($siteInfo['social_links']['youtube']))
          <a href="{{ $siteInfo['social_links']['youtube'] }}" target="_blank" class="youtube"><i class="bi bi-youtube"></i></a>
        @endif
      </div>
    </div>
  </div><!-- End Top Bar -->

  <div class="branding d-flex align-items-center">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-xl-0 text-decoration-none">
        <h1 class="sitename">3S Sorean<span>.</span></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Beranda</a></li>
          <li><a href="#masalah-solusi">Solusi</a></li>
          <li><a href="#fitur">Fitur 3S</a></li>
          <li><a href="#keunggulan">Keunggulan</a></li>
          <li><a href="#indikator">Indikator</a></li>
          <li><a href="#galeri">Galeri</a></li>
          <li><a href="#berita">Berita</a></li>
          <li><a href="#cek-status">Cek Status</a></li>
          <li><a href="#faq">FAQ</a></li>
          <li><a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a></li>
        </ul>
        <i class="mobile-nav-toggle d-none bi bi-list"></i>
      </nav>
    </div>
  </div>

</header>