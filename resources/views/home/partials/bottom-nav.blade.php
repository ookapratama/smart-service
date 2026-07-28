<style>
  .bottom-nav {
    border-top: 1px solid #cbd5e1 !important;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08) !important;
    background-color: #ffffff !important;
    z-index: 1000 !important;
    padding: 6px 0 8px 0 !important;
  }
  .bottom-nav-item {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
    color: #64748b !important;
    padding-top: 6px !important;
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    background: transparent !important;
    -webkit-tap-highlight-color: transparent !important;
  }
  /* Active indicator line placed at top boundary ABOVE the icon */
  .bottom-nav-item.active::before {
    content: "" !important;
    position: absolute !important;
    top: -6px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: 28px !important;
    height: 3px !important;
    background-color: #106eea !important;
    border-radius: 0 0 4px 4px !important;
  }
  .bottom-nav-item i {
    font-size: 1.25rem !important;
    line-height: 1 !important;
    margin-bottom: 3px !important;
  }
  .bottom-nav-item span {
    font-size: 0.7rem !important;
    font-weight: 600 !important;
  }
  .bottom-nav-item.active,
  .bottom-nav-item.active i,
  .bottom-nav-item.active span {
    color: #106eea !important;
  }
</style>

<div class="bottom-nav d-lg-none fixed-bottom bg-white">
  <div class="container d-flex justify-content-around align-items-center text-center">
    <a href="{{ route('home') }}#hero" class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}" data-section="hero">
      <i class="bi bi-house-door d-block"></i>
      <span>Beranda</span>
    </a>
    <a href="{{ route('pengaduan.index') }}" class="bottom-nav-item {{ request()->routeIs('pengaduan.*') ? 'active' : '' }}">
      <i class="bi bi-megaphone d-block"></i>
      <span>Pengaduan</span>
    </a>
    <a href="{{ route('surat.index') }}" class="bottom-nav-item {{ request()->routeIs('surat.*') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-text d-block"></i>
      <span>Surat</span>
    </a>
    <a href="{{ route('home') }}#cek-status" class="bottom-nav-item" data-section="cek-status">
      <i class="bi bi-search d-block"></i>
      <span>Cek Status</span>
    </a>
    <a href="{{ route('panduan') }}" class="bottom-nav-item {{ request()->routeIs('panduan') ? 'active' : '' }}">
      <i class="bi bi-book d-block"></i>
      <span>Panduan</span>
    </a>
  </div>
</div>