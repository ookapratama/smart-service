{{-- Mobile & Tablet Bottom Navigation --}}
<div id="mobile-bottom-nav" class="mobile-bottom-nav">
  <div class="mobile-bottom-nav-inner">

    <a href="#hero" class="bottom-nav-item active" data-section="hero">
      <i class="bi bi-house-door"></i>
      <span>Beranda</span>
    </a>

    <a href="#about" class="bottom-nav-item" data-section="about">
      <i class="bi bi-info-circle"></i>
      <span>Tentang</span>
    </a>

    <a href="#services" class="bottom-nav-item" data-section="services">
      <i class="bi bi-grid"></i>
      <span>Layanan</span>
    </a>

    <a href="#contact" class="bottom-nav-item" data-section="contact">
      <i class="bi bi-envelope"></i>
      <span>Kontak</span>
    </a>

    @auth
      <a href="{{ route('dashboard') }}" class="bottom-nav-item">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    @else
      <a href="{{ route('login') }}" class="bottom-nav-item">
        <i class="bi bi-person-circle"></i>
        <span>Masuk</span>
      </a>
    @endauth

  </div>
</div>
