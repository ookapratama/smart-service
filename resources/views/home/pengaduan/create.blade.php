@extends('home.layouts.app')

@section('title', 'Formulir Layanan Pengaduan & Aspirasi Masyarakat - 3S Soreang')
@section('meta_description', 'Isi formulir pengaduan resmi Kecamatan Soreang. Mudah, transparan, dan terjamin kerahasiaan pelapor.')

@section('content')

  <section class="section light-background py-5" style="background-color: #f4f6f9;">
    <div class="container" style="max-width: 820px;" data-aos="fade-up">

      <!-- HEADER CARD -->
      <div class="card border-0 shadow-sm rounded-4 mb-4" style="border-top: 8px solid #106eea !important;">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
              <i class="bi bi-shield-check me-1"></i> Layanan Resmi 3S
            </span>
          </div>
          <h2 class="fw-bold text-dark mb-2">Formulir Layanan Pengaduan & Aspirasi Masyarakat</h2>
          <p class="text-muted mb-3">
            Silakan isi formulir di bawah ini dengan data yang benar dan dapat dipertanggungjawabkan. Identitas Anda dijamin kerahasiaannya oleh Pemerintah Kecamatan Soreang.
          </p>
          <div class="alert alert-info border-0 rounded-3 mb-0 d-flex align-items-center">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-info flex-shrink-0"></i>
            <small class="mb-0">
              Field bertanda bintang (<span class="text-danger fw-bold">*</span>) wajib diisi. Sebelum mengirim, Anda wajib <strong>memverifikasi nomor WhatsApp</strong> dengan kode OTP.
            </small>
          </div>
        </div>
      </div>

      <!-- MAIN FORM -->
      <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- SECTION 0: CEK NIK (wizard step 1) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0 text-primary">
              <i class="bi bi-person-vcard me-2"></i> 1. Cek NIK
            </h5>
          </div>
          <div class="card-body p-4">

            @if(isset($autofillPemohon) && $autofillPemohon)
              <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-0">
                <i class="bi bi-shield-check-fill fs-3 me-3 text-success flex-shrink-0"></i>
                <div>
                  <strong class="text-success fs-6">Terverifikasi sebagai {{ $autofillPemohon->name }}</strong>
                  <span class="d-block small text-muted">NIK: <code>{{ $autofillPemohon->nik }}</code> &middot; WA: {{ $autofillPemohon->phone }}</span>
                </div>
              </div>
              <input type="hidden" id="nik" name="nik" value="{{ $autofillPemohon->nik }}">
            @else
              <div id="cekNikStep">
                <p class="text-muted small mb-3">
                  Masukkan NIK Anda. Jika NIK sudah pernah terdaftar, kode OTP akan dikirim otomatis ke nomor WhatsApp yang sudah tersimpan.
                </p>
                <div class="row g-2 align-items-end">
                  <div class="col-sm-8">
                    <label for="nik" class="form-label fw-semibold">NIK (No. KTP) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 digit NIK sesuai KTP" required>
                    @error('nik')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-sm-4">
                    <button type="button" class="btn btn-success w-100 rounded-3 fw-semibold" id="btnCekNik">
                      <i class="bi bi-search me-1"></i> Cek NIK
                    </button>
                  </div>
                </div>
                <div class="text-danger small mt-2 d-none" id="cekNikError"></div>
              </div>
            @endif

            <!-- Compact OTP widget: hanya untuk NIK yang sudah ditemukan -->
            <div id="foundOtpStepVerify" class="d-none">
              <div class="alert alert-info border-0 rounded-3 py-2 small" id="foundOtpSentInfo"></div>
              <div class="small mt-n1 mb-2" id="foundOtpSentInfoDebug"></div>
              <div class="row g-2 align-items-end">
                <div class="col-sm-5">
                  <label for="foundOtpCode" class="form-label fw-semibold">Kode OTP (6 digit)</label>
                  <input type="text" class="form-control text-center font-monospace fs-5" id="foundOtpCode" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="______">
                </div>
                <div class="col-sm-4">
                  <button type="button" class="btn btn-primary w-100 rounded-3 fw-semibold" id="btnFoundVerifyOtp">
                    <i class="bi bi-check2-circle me-1"></i> Verifikasi
                  </button>
                </div>
                <div class="col-sm-3">
                  <button type="button" class="btn btn-link text-muted w-100 p-0 pb-2 small" id="btnFoundResendOtp" disabled>
                    Kirim ulang (<span id="foundResendCountdown">60</span>s)
                  </button>
                </div>
              </div>
              <div class="text-danger small mt-2 d-none" id="foundOtpError"></div>

              <div class="alert alert-warning border-0 rounded-3 d-flex align-items-start mt-3 mb-0 py-2">
                <i class="bi bi-headset fs-5 me-2 flex-shrink-0"></i>
                <div class="small">
                  Tidak menerima kode OTP? Nomor WhatsApp yang tersimpan untuk NIK ini mungkin sudah tidak aktif.
                  Hubungi petugas Kecamatan Soreang di <strong>{{ get_setting('profile_telepon') }}</strong> untuk memperbarui nomor WhatsApp Anda, lalu ulangi pengaduan ini.
                </div>
              </div>
            </div>

          </div>
        </div>

        <div id="restOfForm" class="{{ (isset($autofillPemohon) && $autofillPemohon) || $otpVerifiedNik ? '' : 'd-none' }}">

          <!-- SECTION 1: IDENTITAS PEMOHON -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4">
              <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-person-badge me-2"></i> 2. Identitas Pemohon
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-3">

                <div class="col-md-6">
                  <label for="nama" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $autofillPemohon->name ?? '') }}" placeholder="Nama sesuai KTP" required>
                  @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="email" class="form-label fw-semibold">Alamat Email (Opsional)</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $autofillPemohon->email ?? '') }}" placeholder="nama@email.com">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="phone" class="form-label fw-semibold">No. WhatsApp Aktif <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $autofillPemohon->phone ?? '') }}" placeholder="Contoh: 081234567890" required>
                  <small class="text-muted">Kode OTP verifikasi akan dikirim ke nomor ini.</small>
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

          <!-- SECTION 2: RINCIAN PENGADUAN -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4">
              <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-chat-left-text me-2"></i> 3. Rincian Laporan & Pengaduan
              </h5>
            </div>
            <div class="card-body p-4">

              <!-- Banner Nama Pelapor -->
              <div id="nama-pelapor-preview-card" class="alert alert-primary border-0 rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-person-check-fill fs-3 me-3 text-primary flex-shrink-0"></i>
                <div>
                  <span class="text-muted d-block small fw-medium">Identitas Nama Pelapor:</span>
                  <strong class="fs-6 text-primary" id="display-nama-pelapor">
                    {{ old('nama', $autofillPemohon->name ?? '') ?: '(Identitas Terverifikasi System)' }}
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

          <!-- SECTION 3: VERIFIKASI OTP WHATSAPP (hanya jalur NIK belum terdaftar) -->
          <div class="card border-0 shadow-sm rounded-4 mb-4 d-none" id="sectionOtpBottom">
            <div class="card-header bg-white border-bottom p-4">
              <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-whatsapp me-2"></i> 4. Verifikasi Nomor WhatsApp
              </h5>
            </div>
            <div class="card-body p-4">

              <div id="otpStepRequest">
                <p class="text-muted small mb-3">
                  Klik tombol di bawah untuk menerima kode OTP 6 digit melalui WhatsApp. Pastikan NIK dan nomor WhatsApp pada bagian identitas sudah benar.
                </p>
                <button type="button" class="btn btn-success rounded-3 fw-semibold" id="btnRequestOtp">
                  <i class="bi bi-send me-1"></i> Kirim Kode OTP
                </button>
              </div>

              <div id="otpStepVerify" class="d-none">
                <div class="alert alert-info border-0 rounded-3 py-2 small" id="otpSentInfo"></div>
                <div class="small mt-n1 mb-2" id="otpSentInfoDebug"></div>
                <div class="row g-2 align-items-end">
                  <div class="col-sm-5">
                    <label for="otpCode" class="form-label fw-semibold">Kode OTP (6 digit)</label>
                    <input type="text" class="form-control text-center font-monospace fs-5" id="otpCode" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="______">
                  </div>
                  <div class="col-sm-4">
                    <button type="button" class="btn btn-primary w-100 rounded-3 fw-semibold" id="btnVerifyOtp">
                      <i class="bi bi-check2-circle me-1"></i> Verifikasi
                    </button>
                  </div>
                  <div class="col-sm-3">
                    <button type="button" class="btn btn-link text-muted w-100 p-0 pb-2 small" id="btnResendOtp" disabled>
                      Kirim ulang (<span id="resendCountdown">60</span>s)
                    </button>
                  </div>
                </div>
              </div>

              <div id="otpStepDone" class="d-none">
                <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-0">
                  <i class="bi bi-patch-check-fill fs-4 me-3 flex-shrink-0"></i>
                  <div>
                    <strong>Nomor WhatsApp terverifikasi.</strong>
                    <span class="d-block small">Silakan lanjutkan mengirim laporan pengaduan Anda.</span>
                  </div>
                </div>
              </div>

              <div class="text-danger small mt-2 d-none" id="otpError"></div>

            </div>
          </div>

          <!-- SECTION 4: KONFIRMASI -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4">
              <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-shield-check me-2"></i> 5. Pengaturan Privasi & Konfirmasi
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
                <button type="submit" class="btn btn-primary btn-md rounded-3 py-2 fw-bold shadow-sm" id="btnSubmitPengaduan" {{ (isset($autofillPemohon) && $autofillPemohon) || $otpVerifiedNik ? '' : 'disabled' }}>
                  <i class="bi bi-send-fill me-2"></i> Kirim Laporan Pengaduan
                </button>
                <small class="text-muted text-center {{ (isset($autofillPemohon) && $autofillPemohon) || $otpVerifiedNik ? 'd-none' : '' }}" id="submitHint">Verifikasi nomor WhatsApp terlebih dahulu untuk mengaktifkan tombol kirim.</small>
                <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary btn-md rounded-3 py-2">
                  Batal / Kembali
                </a>
              </div>

            </div>
          </div>

        </div>

      </form>

    </div>
  </section>

@endsection

@push('scripts')
<script>
  (function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    function postJson(url, body) {
      return fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify(body)
      }).then(function (res) {
        return res.json().then(function (json) {
          return { status: res.status, json: json };
        });
      });
    }

    function firstErrorMessage(json) {
      var errors = json.errors;
      return errors ? Object.values(errors)[0][0] : json.message;
    }

    function debugCodeHtml(json) {
      if (!json.data || !json.data.debug_code) return '';
      return '<span class="badge bg-warning text-dark">🧪 Mode Testing</span> ' +
        'Kode OTP: <strong>' + json.data.debug_code + '</strong> — WhatsApp tidak benar-benar dikirim di lingkungan ini.';
    }

    function showErrorIn(el, message) {
      el.textContent = message;
      el.classList.remove('d-none');
    }

    function clearErrorIn(el) {
      el.classList.add('d-none');
      el.textContent = '';
    }

    function makeCountdown(btnEl, spanEl) {
      var timer = null;
      return function start(seconds) {
        var left = seconds;
        btnEl.disabled = true;
        spanEl.textContent = left;
        clearInterval(timer);
        timer = setInterval(function () {
          left--;
          spanEl.textContent = left;
          if (left <= 0) {
            clearInterval(timer);
            btnEl.disabled = false;
            btnEl.innerHTML = 'Kirim ulang kode';
          }
        }, 1000);
      };
    }

    var btnSubmit = document.getElementById('btnSubmitPengaduan');
    var submitHint = document.getElementById('submitHint');
    var restOfForm = document.getElementById('restOfForm');
    var sectionOtpBottom = document.getElementById('sectionOtpBottom');
    var nikInput = document.getElementById('nik');
    var displayNamaPelapor = document.getElementById('display-nama-pelapor');

    // --- Wizard step 1: Cek NIK ---------------------------------------
    var btnCekNik = document.getElementById('btnCekNik');
    var cekNikError = document.getElementById('cekNikError');
    var cekNikStep = document.getElementById('cekNikStep');

    var foundOtpStep = document.getElementById('foundOtpStepVerify');
    var foundOtpError = document.getElementById('foundOtpError');
    var foundOtpCode = document.getElementById('foundOtpCode');
    var btnFoundVerify = document.getElementById('btnFoundVerifyOtp');
    var btnFoundResend = document.getElementById('btnFoundResendOtp');
    var startFoundCountdown = makeCountdown(btnFoundResend, document.getElementById('foundResendCountdown'));

    function sendNikCheck(triggerBtn) {
      clearErrorIn(cekNikError);
      var nik = nikInput.value.trim();

      if (nik.length !== 16 || isNaN(nik)) {
        showErrorIn(cekNikError, 'NIK harus 16 digit angka.');
        return;
      }

      triggerBtn.disabled = true;
      postJson('{{ route('otp.request') }}', { nik: nik, purpose: 'pengaduan' }).then(function (result) {
        triggerBtn.disabled = false;

        if (!result.json.success) {
          showErrorIn(cekNikError, firstErrorMessage(result.json));
          return;
        }

        nikInput.readOnly = true;
        cekNikStep.classList.add('d-none');

        if (result.json.data.found) {
          foundOtpStep.classList.remove('d-none');
          document.getElementById('foundOtpSentInfo').innerHTML =
            '<i class="bi bi-whatsapp me-1"></i> Kode OTP telah dikirim ke nomor <strong>' +
            result.json.data.phone_masked + '</strong>. Berlaku 5 menit.';
          document.getElementById('foundOtpSentInfoDebug').innerHTML = debugCodeHtml(result.json);
          startFoundCountdown(60);
        } else {
          restOfForm.classList.remove('d-none');
          sectionOtpBottom.classList.remove('d-none');
        }
      }).catch(function () {
        triggerBtn.disabled = false;
        showErrorIn(cekNikError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    }

    if (btnCekNik) {
      btnCekNik.addEventListener('click', function () { sendNikCheck(btnCekNik); });
    }

    if (btnFoundVerify) {
      btnFoundVerify.addEventListener('click', function () {
        clearErrorIn(foundOtpError);
        var code = foundOtpCode.value.trim();

        if (code.length !== 6) {
          showErrorIn(foundOtpError, 'Masukkan 6 digit kode OTP yang Anda terima.');
          return;
        }

        btnFoundVerify.disabled = true;
        postJson('{{ route('otp.verify') }}', { nik: nikInput.value.trim(), code: code, purpose: 'pengaduan' }).then(function (result) {
          btnFoundVerify.disabled = false;

          if (!result.json.success) {
            showErrorIn(foundOtpError, firstErrorMessage(result.json));
            return;
          }

          var pemohon = (result.json.data && result.json.data.pemohon) || {};
          document.getElementById('nama').value = pemohon.name || '';
          if (displayNamaPelapor) displayNamaPelapor.textContent = pemohon.name || '';
          document.getElementById('email').value = pemohon.email || '';
          document.getElementById('phone').value = pemohon.phone || '';
          document.getElementById('alamat').value = pemohon.alamat || '';
          if (pemohon.kelurahan_id) {
            document.getElementById('kelurahan_id').value = pemohon.kelurahan_id;
          }
          document.getElementById('phone').readOnly = true;

          foundOtpStep.classList.add('d-none');
          restOfForm.classList.remove('d-none');
          if (submitHint) submitHint.classList.add('d-none');
          if (btnSubmit) btnSubmit.disabled = false;
        }).catch(function () {
          btnFoundVerify.disabled = false;
          showErrorIn(foundOtpError, 'Gagal menghubungi server. Periksa koneksi Anda.');
        });
      });
    }

    if (btnFoundResend) {
      btnFoundResend.addEventListener('click', function () { sendNikCheck(btnFoundResend); });
    }

    // --- Widget OTP bawah (jalur NIK belum terdaftar) -----------------------
    var btnRequest = document.getElementById('btnRequestOtp');
    var btnVerify = document.getElementById('btnVerifyOtp');
    var btnResend = document.getElementById('btnResendOtp');
    var otpError = document.getElementById('otpError');
    var startCountdown = makeCountdown(btnResend, document.getElementById('resendCountdown'));

    function identityPayload() {
      return {
        nik: nikInput.value.trim(),
        phone: document.getElementById('phone').value.trim(),
        purpose: 'pengaduan'
      };
    }

    function requestOtp(btn) {
      clearErrorIn(otpError);
      var payload = identityPayload();

      if (payload.nik.length !== 16 || !payload.phone) {
        showErrorIn(otpError, 'Lengkapi NIK (16 digit) dan nomor WhatsApp pada bagian identitas terlebih dahulu.');
        return;
      }

      btn.disabled = true;
      postJson('{{ route('otp.request') }}', payload).then(function (result) {
        btn.disabled = false;
        if (!result.json.success) {
          showErrorIn(otpError, firstErrorMessage(result.json));
          return;
        }
        document.getElementById('otpStepRequest').classList.add('d-none');
        document.getElementById('otpStepVerify').classList.remove('d-none');
        document.getElementById('otpSentInfo').innerHTML =
          '<i class="bi bi-whatsapp me-1"></i> Kode OTP telah dikirim ke nomor <strong>' +
          result.json.data.phone_masked + '</strong>. Berlaku 5 menit.';
        document.getElementById('otpSentInfoDebug').innerHTML = debugCodeHtml(result.json);
        startCountdown(60);
      }).catch(function () {
        btn.disabled = false;
        showErrorIn(otpError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    }

    if (btnRequest) btnRequest.addEventListener('click', function () { requestOtp(btnRequest); });
    if (btnResend) btnResend.addEventListener('click', function () { requestOtp(btnResend); });

    if (btnVerify) {
      btnVerify.addEventListener('click', function () {
        clearErrorIn(otpError);
        var code = document.getElementById('otpCode').value.trim();

        if (code.length !== 6) {
          showErrorIn(otpError, 'Masukkan 6 digit kode OTP yang Anda terima.');
          return;
        }

        btnVerify.disabled = true;
        var payload = identityPayload();
        payload.code = code;

        postJson('{{ route('otp.verify') }}', payload).then(function (result) {
          btnVerify.disabled = false;

          if (!result.json.success) {
            showErrorIn(otpError, firstErrorMessage(result.json));
            return;
          }

          document.getElementById('otpStepVerify').classList.add('d-none');
          document.getElementById('otpStepDone').classList.remove('d-none');
          if (submitHint) submitHint.classList.add('d-none');
          if (btnSubmit) btnSubmit.disabled = false;
        }).catch(function () {
          btnVerify.disabled = false;
          showErrorIn(otpError, 'Gagal menghubungi server. Periksa koneksi Anda.');
        });
      });
    }

    // Nama input listener update preview card
    var namaInput = document.getElementById('nama');
    if (namaInput && displayNamaPelapor) {
      namaInput.addEventListener('input', function () {
        displayNamaPelapor.textContent = this.value.trim() || '(Identitas Terverifikasi System)';
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

  })();
</script>
@endpush
