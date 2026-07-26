<header id="header" class="header sticky-top">

  <!-- Top Bar -->
  <div class="topbar d-flex align-items-center">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-envelope d-flex align-items-center">
          <a href="mailto:{{ $siteInfo['email'] ?? 'contact@example.com' }}">{{ $siteInfo['email'] ?? 'contact@example.com' }}</a>
        </i>
        <i class="bi bi-phone d-flex align-items-center ms-4">
          <span>{{ $siteInfo['phone'] ?? '+1 5589 55488 55' }}</span>
        </i>
      </div>
      <div class="social-links d-none d-md-flex align-items-center">
        @if(!empty($siteInfo['social_links']['twitter']))
          <a href="{{ $siteInfo['social_links']['twitter'] }}" class="twitter"><i class="bi bi-twitter-x"></i></a>
        @endif
        @if(!empty($siteInfo['social_links']['facebook']))
          <a href="{{ $siteInfo['social_links']['facebook'] }}" class="facebook"><i class="bi bi-facebook"></i></a>
        @endif
        @if(!empty($siteInfo['social_links']['instagram']))
          <a href="{{ $siteInfo['social_links']['instagram'] }}" class="instagram"><i class="bi bi-instagram"></i></a>
        @endif
        @if(!empty($siteInfo['social_links']['linkedin']))
          <a href="{{ $siteInfo['social_links']['linkedin'] }}" class="linkedin"><i class="bi bi-linkedin"></i></a>
        @endif
      </div>
    </div>
  </div><!-- End Top Bar -->

  <div class="branding d-flex align-items-center">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="{{ route('home') }}" class="logo d-flex align-items-center">
        @if(!empty($siteInfo['logo']))
          <img src="{{ $siteInfo['logo'] }}" alt="{{ $siteInfo['name'] ?? 'BizLand' }}">
        @else
          <h1 class="sitename">{{ $siteInfo['name'] ?? 'BizLand' }}<span>.</span></h1>
        @endif
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#portfolio">Portfolio</a></li>
          <li><a href="#team">Team</a></li>
          <li class="dropdown">
            <a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown">
                <a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-none bi bi-list"></i>
      </nav>
    </div>
  </div>

</header>
