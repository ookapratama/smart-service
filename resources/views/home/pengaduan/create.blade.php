@extends('home.layouts.app')

@section('title', 'Formulir Layanan Pengaduan & Aspirasi Masyarakat - 3S Soreang')
@section('meta_description', 'Isi formulir pengaduan resmi Kecamatan Soreang. Mudah, transparan, dan terjamin kerahasiaan pelapor.')

@section('content')

  <section class="section light-background py-5" style="background-color: #f4f6f9;">
    <div class="container" style="max-width: 820px;" data-aos="fade-up">

      <!-- HEADER GOOGLE FORM INSPIRED CARD -->
      <div class="card border-0 shadow-sm rounded-4 mb-4" style="border-top: 8px solid #106eea !important;">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
              <i class="bi bi-shield-check me-1"></i> Layanan Resmi 3S
            </span>
          </div>
          <h2 class="fw-bold text-dark mb-2">Formulir Layanan Pengaduan, Aspirasi & Informasi Masyarakat</h2>
          <p class="text-muted mb-3">
            Silakan isi formulir di bawah ini dengan data yang benar dan dapat dipertanggungjawabkan. Identitas Anda dijamin kerahasiaannya oleh Pemerintah Kecamatan Soreang.
          </p>
          <div class="alert alert-info border-0 rounded-3 mb-0 d-flex align-items-center">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-info flex-shrink-0"></i>
            <small class="mb-0">
              Field bertanda bintang (<span class="text-danger fw-bold">*</span>) wajib diisi. Tiket pengaduan resmi akan diterbitkan otomatis setelah Anda mengirim formulir ini.
            </small>
          </div>
        </div>
      </div>

      <!-- MAIN FORM -->
      <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- SECTION 1: DATA PELAPOR -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0 text-primary">
              <i class="bi bi-person-badge me-2"></i> 1. Identitas Pelapor
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              
              @if(isset($autofillPemohon) && $autofillPemohon)
                <div class="col-12">
                  <div class="alert alert-success border-0 rounded-3 p-3 mb-0 d-flex align-items-center">
                    <i class="bi bi-shield-check-fill fs-3 me-3 text-success flex-shrink-0"></i>
                    <div>
                      <strong class="text-success fs-6">Terverifikasi sebagai {{ $autofillPemohon->name }}</strong>
                      <span class="d-block small text-muted">NIK: <code>{{ $autofillPemohon->nik }}</code> &middot; WA: {{ $autofillPemohon->phone }}</span>
                    </div>
                  </div>
                  <input type="hidden" name="nik" value="{{ $autofillPemohon->nik }}">
                </div>
              @else
                <!-- NIK Input & Verifikasi Button -->
                <div class="col-12">
                  <label for="nik" class="form-label fw-semibold">NIK (No. KTP) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="text" class="form-control form-control-lg fs-6 @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="Masukkan 16 digit NIK sesuai KTP" required>
                    <button class="btn btn-primary fw-bold px-4" type="button" id="btn-verifikasi-nik">
                      <i class="bi bi-shield-check me-1"></i> Verifikasi NIK
                    </button>
                  </div>
                  <div id="nik-feedback" class="mt-2"></div>
                  @error('nik')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
              @endif

              <!-- Container Input Form Pemohon Baru / Terverifikasi -->
              <div id="pemohon-fields-container" class="col-12 border-top pt-3" style="display: {{ (isset($autofillPemohon) && $autofillPemohon) || old('nik') || $errors->any() ? 'block' : 'none' }};">
                <div class="alert alert-light border rounded-3 p-3 mb-0">
                  <div class="d-flex align-items-center gap-2 mb-3 text-primary fw-semibold border-bottom pb-2">
                    <i class="bi bi-person-lines-fill"></i> Rincian Data Pelapor
                  </div>
                  <div class="row g-3">
                    
                    <div class="col-md-6">
                      <label for="nama" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                      <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $autofillPemohon->name ?? '') }}" placeholder="Contoh: Ahmad Subagja">
                      @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $autofillPemohon->email ?? '') }}" placeholder="nama@email.com">
                      @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label for="phone" class="form-label fw-semibold">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                      <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $autofillPemohon->phone ?? '') }}" placeholder="Contoh: 081234567890">
                      @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label for="kelurahan_id" class="form-label fw-semibold">Kelurahan / Desa Asal</label>
                      <select class="form-select @error('kelurahan_id') is-invalid @enderror" id="kelurahan_id" name="kelurahan_id">
                        <option value="">Pilih Kelurahan</option>
                        @foreach($kelurahanList as $k)
                          <option value="{{ $k->id }}" {{ old('kelurahan_id', $autofillPemohon->kelurahan_id ?? '') == $k->id ? 'selected' : '' }}>Kelurahan {{ $k->nama }}</option>
                        @endforeach
                      </select>
                      @error('kelurahan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12">
                      <label for="alamat" class="form-label fw-semibold">Alamat Lengkap Tempat Tinggal</label>
                      <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ old('alamat', $autofillPemohon->alamat ?? '') }}" placeholder="Jl. Raya Soreang No. 12 RT 02/05">
                      @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- SECTION 2: DETAIL LAPORAN PENGADUAN -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0 text-primary">
              <i class="bi bi-chat-left-text me-2"></i> 2. Rincian Laporan & Pengaduan
            </h5>
          </div>
          <div class="card-body p-4">

            <!-- Banner / Badge Nama Pelapor Terverifikasi -->
            <div id="nama-pelapor-preview-card" class="alert alert-primary border-0 rounded-3 mb-4 d-flex align-items-center">
              <i class="bi bi-person-check-fill fs-3 me-3 text-primary flex-shrink-0"></i>
              <div>
                <span class="text-muted d-block small fw-medium">Identitas Nama Pelapor:</span>
                <strong class="fs-6 text-primary" id="display-nama-pelapor">
                  {{ old('nama', $autofillPemohon->name ?? '') ?: '(Belum diverifikasi - silakan masukkan NIK dan klik Verifikasi NIK)' }}
                </strong>
              </div>
            </div>

            <div class="row g-3">

              <div class="col-12 mb-2">
                <label class="form-label fw-semibold">Jenis Laporan / Topik <span class="text-danger">*</span></label>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="border rounded-3 p-3 d-flex align-items-center h-100 bg-white shadow-sm" for="jenis1" style="cursor: pointer;">
                      <input class="form-check-input me-3 mt-0 flex-shrink-0" type="radio" name="jenis_laporan" id="jenis1" value="Pengaduan / Keluhan" {{ old('jenis_laporan', 'Pengaduan / Keluhan') == 'Pengaduan / Keluhan' ? 'checked' : '' }}>
                      <span class="fw-bold text-dark">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Pengaduan / Keluhan
                      </span>
                    </label>
                  </div>

                  <div class="col-md-4">
                    <label class="border rounded-3 p-3 d-flex align-items-center h-100 bg-white shadow-sm" for="jenis2" style="cursor: pointer;">
                      <input class="form-check-input me-3 mt-0 flex-shrink-0" type="radio" name="jenis_laporan" id="jenis2" value="Aspirasi / Usulan" {{ old('jenis_laporan') == 'Aspirasi / Usulan' ? 'checked' : '' }}>
                      <span class="fw-bold text-dark">
                        <i class="bi bi-lightbulb-fill text-warning me-1"></i> Aspirasi / Usulan
                      </span>
                    </label>
                  </div>

                  <div class="col-md-4">
                    <label class="border rounded-3 p-3 d-flex align-items-center h-100 bg-white shadow-sm" for="jenis3" style="cursor: pointer;">
                      <input class="form-check-input me-3 mt-0 flex-shrink-0" type="radio" name="jenis_laporan" id="jenis3" value="Kritik & Saran" {{ old('jenis_laporan') == 'Kritik & Saran' ? 'checked' : '' }}>
                      <span class="fw-bold text-dark">
                        <i class="bi bi-chat-left-dots-fill text-primary me-1"></i> Kritik & Saran
                      </span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <label for="kategori_pengaduan_id" class="form-label fw-semibold">Kategori Pengaduan <span class="text-danger">*</span></label>
                <select class="form-select @error('kategori_pengaduan_id') is-invalid @enderror" id="kategori_pengaduan_id" name="kategori_pengaduan_id" required>
                  <option value="">Pilih Kategori Pengaduan</option>
                  @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_pengaduan_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                  @endforeach
                </select>
                @error('kategori_pengaduan_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label for="judul" class="form-label fw-semibold">Judul Laporan / Subjek <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Jalan Berlubang di Dekat Pasar Soreang" required>
                @error('judul')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label for="lokasi" class="form-label fw-semibold">Lokasi Kejadian / Objek Laporan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Pertigaan Jalan Raya Soreang KM 2" required>
                @error('lokasi')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label for="tanggal_kejadian" class="form-label fw-semibold">Tanggal Kejadian</label>
                <input type="date" class="form-control @error('tanggal_kejadian') is-invalid @enderror" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}">
                @error('tanggal_kejadian')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="deskripsi" class="form-label fw-semibold">Uraian Kronologi / Isi Pengaduan Lengkap <span class="text-danger">*</span></label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan masalah secara rinci termasuk dampak yang dialami dan harapan tindakan lanjut..." required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="lampiran" class="form-label fw-semibold">Lampirkan Bukti Foto / Dokumen Pendukung (Opsional)</label>
                <input type="file" class="form-control @error('lampiran') is-invalid @enderror" id="lampiran" name="lampiran" accept="image/*,.pdf">
                <small class="text-muted mt-1 d-block">Format: JPG, PNG, WEBP, atau PDF. Maksimal ukuran file: 3 MB.</small>
                <div id="lampiran-preview" class="mt-2"></div>
                @error('lampiran')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

            </div>
          </div>
        </div>

        <!-- SECTION 3: PRIVASI & PERSETUJUAN -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0 text-primary">
              <i class="bi bi-shield-check me-2"></i> 3. Pengaturan Privasi & Konfirmasi
            </h5>
          </div>
          <div class="card-body p-4">
            
            <div class="form-check form-switch p-3 bg-light rounded-3 mb-3 border">
              <input class="form-check-input ms-0 me-3 fs-5" type="checkbox" id="is_anonim" name="is_anonim" value="1" {{ old('is_anonim') ? 'checked' : '' }}>
              <label class="form-check-label" for="is_anonim">
                <strong class="text-dark">Rahasiakan Identitas Saya (Laporan Anonim)</strong>
                <span class="d-block text-muted small">Jika dicentang, nama Anda tidak akan dipublikasikan dan disembunyikan pada ringkasan laporan publik.</span>
              </label>
            </div>

            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="setuju" name="setuju" required checked>
              <label class="form-check-label text-muted small" for="setuju">
                Saya menyatakan bahwa informasi dan dokumen pengaduan yang saya sampaikan adalah benar dan dapat dipertanggungjawabkan secara hukum.
              </label>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-md rounded-3 py-2 fw-bold shadow-sm">
                <i class="bi bi-send-fill me-2"></i> Kirim Laporan Pengaduan
              </button>
              <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary btn-md rounded-3 py-2">
                Batal / Kembali
              </a>
            </div>

          </div>
        </div>

      </form>

    </div>
  </section>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const nikInput = document.getElementById('nik');
    const btnVerifikasi = document.getElementById('btn-verifikasi-nik');
    const nikFeedback = document.getElementById('nik-feedback');
    const pemohonContainer = document.getElementById('pemohon-fields-container');
    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const kelurahanSelect = document.getElementById('kelurahan_id');
    const alamatInput = document.getElementById('alamat');
    const displayNama = document.getElementById('display-nama-pelapor');

    function updateNamaDisplay(name) {
      if (displayNama) {
        displayNama.textContent = name && name.trim() !== '' 
          ? name 
          : '(Belum diverifikasi - silakan masukkan NIK dan klik Verifikasi NIK)';
      }
    }

    if (namaInput) {
      namaInput.addEventListener('input', function () {
        updateNamaDisplay(this.value);
      });
    }

    async function checkNik() {
      const nik = nikInput.value.trim();
      if (!nik) {
        nikFeedback.innerHTML = '<div class="alert alert-warning py-2 mb-0 small"><i class="bi bi-exclamation-circle me-1"></i> Silakan masukkan NIK 16 digit.</div>';
        return;
      }
      if (nik.length !== 16 || isNaN(nik)) {
        nikFeedback.innerHTML = '<div class="alert alert-danger py-2 mb-0 small"><i class="bi bi-x-circle me-1"></i> NIK harus terdiri dari 16 digit angka.</div>';
        return;
      }

      nikFeedback.innerHTML = '<div class="alert alert-info py-2 mb-0 small d-flex align-items-center"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Memeriksa NIK ke sistem...</div>';
      btnVerifikasi.disabled = true;

      try {
        const response = await fetch("{{ route('pengaduan.cek-nik') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ nik: nik })
        });

        const data = await response.json();
        btnVerifikasi.disabled = false;

        if (response.ok && data.exists) {
          nikFeedback.innerHTML = `<div class="alert alert-success py-2 mb-0 small d-flex align-items-center"><i class="bi bi-check-circle-fill me-2 fs-6"></i> NIK Terverifikasi! Pemohon: <strong>${data.pemohon.name}</strong></div>`;
          
          if (namaInput) namaInput.value = data.pemohon.name || '';
          if (emailInput) emailInput.value = data.pemohon.email || '';
          if (phoneInput) phoneInput.value = data.pemohon.phone || '';
          if (kelurahanSelect && data.pemohon.kelurahan_id) {
            kelurahanSelect.value = data.pemohon.kelurahan_id;
          }
          if (alamatInput) alamatInput.value = data.pemohon.alamat || '';

          updateNamaDisplay(data.pemohon.name);
          pemohonContainer.style.display = 'block';

        } else {
          nikFeedback.innerHTML = `<div class="alert alert-info py-2 mb-0 small"><i class="bi bi-info-circle me-1"></i> NIK belum terdaftar di database. Silakan isi data pelapor pada form di bawah ini.</div>`;
          pemohonContainer.style.display = 'block';
          updateNamaDisplay(namaInput ? namaInput.value : '');
        }
      } catch (error) {
        btnVerifikasi.disabled = false;
        nikFeedback.innerHTML = '<div class="alert alert-danger py-2 mb-0 small"><i class="bi bi-exclamation-triangle me-1"></i> Terjadi kesalahan jaringan saat verifikasi NIK.</div>';
      }
    }

    if (btnVerifikasi) {
      btnVerifikasi.addEventListener('click', checkNik);
    }

    if (nikInput) {
      nikInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          checkNik();
        }
      });
    }

    // Live Preview Lampiran Gambar / Dokumen
    const lampiranInput = document.getElementById('lampiran');
    const lampiranPreview = document.getElementById('lampiran-preview');
    if (lampiranInput && lampiranPreview) {
      lampiranInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function (e) {
            lampiranPreview.innerHTML = `
              <div class="p-2 border rounded-3 bg-light d-inline-block shadow-sm mt-1">
                <div class="small fw-semibold text-primary mb-1"><i class="bi bi-image me-1"></i> Preview Lampiran Foto:</div>
                <img src="${e.target.result}" class="rounded-2" style="max-height: 160px; max-width: 100%; object-fit: contain;">
              </div>
            `;
          };
          reader.readAsDataURL(file);
        } else if (file) {
          lampiranPreview.innerHTML = `
            <div class="p-2 border rounded-3 bg-light small text-muted mt-1">
              <i class="bi bi-file-earmark-pdf text-danger me-1 fs-6"></i> File Lampiran: <strong>${file.name}</strong> (${(file.size / 1024 / 1024).toFixed(2)} MB)
            </div>
          `;
        } else {
          lampiranPreview.innerHTML = '';
        }
      });
    }

    @if(old('nik'))
      checkNik();
    @endif
  });
  </script>

@endsection
