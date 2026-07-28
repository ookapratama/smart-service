<header id="header" class="header sticky-top" style="z-index: 9999 !important;">

  <!-- Top Bar -->
  <div class="topbar d-flex align-items-center bg-primary text-white py-2" style="font-size: 0.85rem;">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center gap-3">
        <span class="d-flex align-items-center">
          <i class="bi bi-envelope me-1"></i>
          <a href="mailto:{{ $siteInfo['email'] ?? 'layanan@soreang.parepare.go.id' }}" class="text-white text-decoration-none">{{ $siteInfo['email'] ?? 'layanan@soreang.parepare.go.id' }}</a>
        </span>
        <span class="d-flex align-items-center ms-3">
          <i class="bi bi-telephone me-1"></i>
          <span class="text-white">{{ $siteInfo['phone'] ?? '(0421) 21055' }}</span>
        </span>
      </div>
      <div class="social-links d-none d-md-flex align-items-center gap-3">
        @if(!empty($siteInfo['social_links']['instagram']))
          <a href="{{ $siteInfo['social_links']['instagram'] }}" target="_blank" class="text-white opacity-75 hover-opacity-100"><i class="bi bi-instagram"></i></a>
        @endif
        @if(!empty($siteInfo['social_links']['facebook']))
          <a href="{{ $siteInfo['social_links']['facebook'] }}" target="_blank" class="text-white opacity-75 hover-opacity-100"><i class="bi bi-facebook"></i></a>
        @endif
        @if(!empty($siteInfo['social_links']['youtube']))
          <a href="{{ $siteInfo['social_links']['youtube'] }}" target="_blank" class="text-white opacity-75 hover-opacity-100"><i class="bi bi-youtube"></i></a>
        @endif
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteInfo['whatsapp'] ?? '6281234567890') }}" target="_blank" class="text-white opacity-75 hover-opacity-100"><i class="bi bi-whatsapp"></i></a>
      </div>
    </div>
  </div><!-- End Top Bar -->

  <div class="branding d-flex align-items-center bg-white shadow-sm py-2">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      
      <!-- Brand & Logo -->
      <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-xl-0 text-decoration-none">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; font-weight: 800; font-size: 1.1rem;">
            3S
          </div>
          <div>
            <h1 class="sitename fs-5 fw-bold text-dark m-0 leading-tight">Kecamatan Soreang</h1>
            <small class="text-muted font-monospace d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">PAREPARE DIGITAL SERVICE</small>
          </div>
        </div>
      </a>

      <!-- Main Navigation Menu -->
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ route('home') }}#hero" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
          <li><a href="{{ route('home') }}#profil">Profil</a></li>
          <li><a href="{{ route('home') }}#jelajahi">Jelajahi</a></li>
          <li><a href="{{ route('home') }}#services">Fitur 3S</a></li>
          <li><a href="{{ route('pengaduan.index') }}" class="{{ request()->routeIs('pengaduan.*') ? 'active' : '' }}">Pengaduan</a></li>
          <li><a href="{{ route('berita.public.index') }}" class="{{ request()->routeIs('berita.public.*') ? 'active' : '' }}">Berita</a></li>
          <li><a href="{{ route('cek-status.index') }}" class="{{ request()->routeIs('cek-status.*') ? 'active' : '' }}">Cek Status</a></li>
          <li><a href="{{ route('home') }}#faq">FAQ</a></li>
          <li>
            <a href="{{ route('login') }}" class="btn btn-sm btn-primary text-white rounded-pill px-3 py-1 ms-lg-2">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login Admin
            </a>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-none bi bi-list fs-3"></i>
      </nav>

    </div>
  </div>

</header>