@extends('home.layouts.app')

@section('title', 'Layanan Persuratan Online - Soreang Smart Service (3S)')
@section('meta_description', 'Ajukan surat keterangan dan surat pengantar Kecamatan Soreang secara online. Tanpa antre, terverifikasi WhatsApp, dan dapat dipantau statusnya.')

@section('content')

  <!-- HERO PERSURATAN -->
  <section class="hero section light-background py-5" style="background: linear-gradient(135deg, #eef2ff 0%, #f0f7ff 100%);">
    <div class="container py-4">
      <div class="row align-items-center gy-4">
        <div class="col-lg-7" data-aos="fade-up">
          <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill mb-3">
            <i class="bi bi-file-earmark-text me-1"></i> Layanan Persuratan Resmi 3S
          </span>
          <h1 class="display-5 fw-bold text-dark mb-3">
            Ajukan Surat Keterangan & Pengantar Secara Online
          </h1>
          <p class="lead text-muted mb-4">
            Pilih jenis surat, isi formulir, verifikasi nomor WhatsApp Anda dengan kode OTP, dan pantau proses penerbitan surat melalui nomor tiket — tanpa perlu antre di kantor kecamatan.
          </p>
          <a href="#daftar-surat" class="btn btn-primary btn-md px-4 py-3 shadow-sm rounded-3 fw-bold">
            <i class="bi bi-pencil-square me-2"></i> Pilih Jenis Surat
          </a>
        </div>
        <div class="col-lg-5 text-center" data-aos="fade-up" data-aos-delay="200">
          <div class="bg-white p-4 rounded-4 shadow-lg border border-light">
            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
              <i class="bi bi-envelope-paper fs-1"></i>
            </div>
            <h4 class="fw-bold mb-2">Alur Pengajuan Surat</h4>
            <ol class="text-start text-muted small mb-3 ps-3">
              <li class="mb-1">Pilih jenis surat &amp; lengkapi formulir</li>
              <li class="mb-1">Verifikasi nomor WhatsApp via kode OTP</li>
              <li class="mb-1">Terima nomor tiket via WhatsApp</li>
              <li>Petugas memverifikasi &amp; menerbitkan surat</li>
            </ol>
            <a href="{{ route('cek-status.index') }}" class="btn btn-sm btn-outline-secondary w-100">
              <i class="bi bi-search me-1"></i> Cek Status Pengajuan
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- DAFTAR JENIS SURAT -->
  <section id="daftar-surat" class="section">
    <div class="container" data-aos="fade-up">
      @include('home.components.section-title', [
        'subtitle' => 'Jenis Layanan',
        'title' => 'Pilih Jenis Surat yang Anda Butuhkan',
        'description' => 'Setiap jenis surat memiliki persyaratan data dan lampiran yang berbeda. Siapkan berkas Anda sebelum mengisi formulir.'
      ])

      <div class="row gy-4">
        @forelse($jenisSuratList as $jenis)
          @php
            $requiredFields = collect($jenis->fields ?? [])->where('required', true);
            $fileFields = $requiredFields->where('type', 'file');
            $dataFields = $requiredFields->where('type', '!=', 'file');
          @endphp
          <div class="col-md-6 col-lg-4">
            <div class="p-4 bg-white border rounded-4 h-100 shadow-sm d-flex flex-column">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                  <i class="bi bi-file-earmark-text fs-4"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-0">{{ $jenis->nama }}</h5>
                  <small class="text-muted font-monospace">{{ $jenis->kode }}</small>
                </div>
              </div>
              <p class="text-muted small mb-3">{{ $jenis->deskripsi }}</p>

              @if($dataFields->isNotEmpty() || $fileFields->isNotEmpty())
                <div class="mb-3">
                  <span class="fw-semibold small text-dark d-block mb-1">Persyaratan:</span>
                  <ul class="text-muted small mb-0 ps-3">
                    @foreach($dataFields as $field)
                      <li>{{ $field['label'] ?? \Illuminate\Support\Str::headline($field['name']) }}</li>
                    @endforeach
                    @foreach($fileFields as $field)
                      <li><i class="bi bi-paperclip"></i> Lampiran: {{ $field['label'] ?? \Illuminate\Support\Str::headline($field['name']) }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <a href="{{ route('surat.create', $jenis) }}" class="btn btn-outline-primary rounded-3 fw-semibold mt-auto">
                <i class="bi bi-pencil-square me-1"></i> Ajukan Surat Ini
              </a>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-inbox display-4 d-block mb-3"></i>
            Belum ada jenis surat yang dapat diajukan saat ini.
          </div>
        @endforelse
      </div>
    </div>
  </section>

@endsection
