@extends('home.layouts.app')

@section('title', 'Formulir Pengajuan ' . $jenisSurat->nama . ' - 3S Soreang')
@section('meta_description', 'Formulir pengajuan ' . $jenisSurat->nama . ' online Kecamatan Soreang dengan verifikasi WhatsApp OTP.')

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
            <span class="badge bg-light text-dark border px-3 py-1 rounded-pill font-monospace">{{ $jenisSurat->kode }}</span>
          </div>
          <h2 class="fw-bold text-dark mb-2">Formulir Pengajuan {{ $jenisSurat->nama }}</h2>
          <p class="text-muted mb-3">{{ $jenisSurat->deskripsi }}</p>
          <div class="alert alert-info border-0 rounded-3 mb-0 d-flex align-items-center">
            <i class="bi bi-whatsapp fs-4 me-3 text-info flex-shrink-0"></i>
            <small class="mb-0">
              Field bertanda bintang (<span class="text-danger fw-bold">*</span>) wajib diisi. Sebelum mengirim, Anda wajib <strong>memverifikasi nomor WhatsApp</strong> dengan kode OTP — surat adalah dokumen legal atas nama Anda.
            </small>
          </div>
        </div>
      </div>

      @if ($errors->has('otp'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
          <i class="bi bi-shield-exclamation fs-4 me-3 flex-shrink-0"></i>
          <div>{{ $errors->first('otp') }}</div>
        </div>
      @endif

      <!-- MAIN FORM -->
      <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data" id="formSurat">
        @csrf
        <input type="hidden" name="jenis_surat_id" value="{{ $jenisSurat->id }}">

        <!-- SECTION 0: CEK NIK (wizard step 1) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0 text-primary">
              <i class="bi bi-person-vcard me-2"></i> 1. Cek NIK
            </h5>
          </div>
          <div class="card-body p-4">

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

            <!-- Compact OTP widget: hanya untuk NIK yang sudah ditemukan -->
            <div id="foundOtpStepVerify" class="d-none">
              <div class="alert alert-info border-0 rounded-3 py-2 small" id="foundOtpSentInfo"></div>
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

              <!-- Fallback: nomor WA lama tak aktif — tidak ada self-service ganti nomor,
                   sesuai keputusan produk (§4): koreksi nomor hanya lewat petugas. -->
              <div class="alert alert-warning border-0 rounded-3 d-flex align-items-start mt-3 mb-0 py-2">
                <i class="bi bi-headset fs-5 me-2 flex-shrink-0"></i>
                <div class="small">
                  Tidak menerima kode OTP? Nomor WhatsApp yang tersimpan untuk NIK ini mungkin sudah tidak aktif.
                  Hubungi petugas Kecamatan Soreang di <strong>{{ get_setting('profile_telepon') }}</strong>
                  untuk memperbarui nomor WhatsApp Anda, lalu ulangi pengajuan surat ini.
                </div>
              </div>
            </div>

          </div>
        </div>

        <div id="restOfForm" class="d-none">

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
                  <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Nama sesuai KTP" required>
                  @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="phone" class="form-label fw-semibold">No. WhatsApp Aktif <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" required>
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
                      <option value="{{ $k->id }}" {{ old('kelurahan_id') == $k->id ? 'selected' : '' }}>Kelurahan {{ $k->nama }}</option>
                    @endforeach
                  </select>
                  @error('kelurahan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-12">
                  <label for="alamat" class="form-label fw-semibold">Alamat Lengkap Tempat Tinggal</label>
                  <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ old('alamat') }}" placeholder="Jl. Raya Soreang No. 12 RT 02/05">
                  @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

              </div>
            </div>
          </div>

          <!-- SECTION 2: DATA SURAT -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4">
              <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-file-earmark-text me-2"></i> 3. Data {{ $jenisSurat->nama }}
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-3">

                @foreach($jenisSurat->fields ?? [] as $field)
                  @php
                    $name = $field['name'] ?? null;
                    if (! $name) continue;
                    $label = $field['label'] ?? \Illuminate\Support\Str::headline($name);
                    $required = $field['required'] ?? false;
                    $type = $field['type'] ?? 'text';
                    $inputName = "data[{$name}]";
                    $errorKey = "data.{$name}";
                  @endphp
                  <div class="col-12 {{ in_array($type, ['textarea', 'file']) ? '' : 'col-md-6' }}">
                    <label for="field_{{ $name }}" class="form-label fw-semibold">
                      {{ $label }} @if($required)<span class="text-danger">*</span>@endif
                    </label>

                    @switch($type)
                      @case('textarea')
                        <textarea class="form-control @error($errorKey) is-invalid @enderror" id="field_{{ $name }}" name="{{ $inputName }}" rows="3" @if($required) required @endif>{{ old($errorKey) }}</textarea>
                        @break

                      @case('number')
                        <input type="number" class="form-control @error($errorKey) is-invalid @enderror" id="field_{{ $name }}" name="{{ $inputName }}" value="{{ old($errorKey) }}" @if($required) required @endif>
                        @break

                      @case('date')
                        <input type="date" class="form-control @error($errorKey) is-invalid @enderror" id="field_{{ $name }}" name="{{ $inputName }}" value="{{ old($errorKey) }}" @if($required) required @endif>
                        @break

                      @case('select')
                        <select class="form-select @error($errorKey) is-invalid @enderror" id="field_{{ $name }}" name="{{ $inputName }}" @if($required) required @endif>
                          <option value="">Pilih {{ $label }}</option>
                          @foreach($field['options'] ?? [] as $option)
                            <option value="{{ $option }}" {{ old($errorKey) == $option ? 'selected' : '' }}>{{ $option }}</option>
                          @endforeach
                        </select>
                        @break

                      @case('file')
                        <input type="file" class="form-control @error($errorKey) is-invalid @enderror" id="field_{{ $name }}" name="{{ $inputName }}" accept=".jpg,.jpeg,.png,.pdf" @if($required) required @endif>
                        <small class="text-muted mt-1 d-block">Format: JPG, JPEG, PNG, atau PDF. Maksimal ukuran file: 2 MB.</small>
                        @break

                      @default
                        <input type="text" class="form-control @error($errorKey) is-invalid @enderror" id="field_{{ $name }}" name="{{ $inputName }}" value="{{ old($errorKey) }}" @if($required) required @endif>
                    @endswitch

                    @error($errorKey)
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                @endforeach

                <div class="col-12">
                  <label for="keperluan" class="form-label fw-semibold">Keperluan Pengajuan (Opsional)</label>
                  <input type="text" class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan" value="{{ old('keperluan') }}" placeholder="Contoh: Persyaratan melamar pekerjaan" maxlength="500">
                  @error('keperluan')
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
                    <span class="d-block small">Silakan lanjutkan mengirim pengajuan surat Anda.</span>
                  </div>
                </div>
              </div>

              <div class="text-danger small mt-2 d-none" id="otpError"></div>

            </div>
          </div>

          <!-- SECTION 4: KONFIRMASI -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
              <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="setuju" name="setuju" required checked>
                <label class="form-check-label text-muted small" for="setuju">
                  Saya menyatakan bahwa data dan dokumen yang saya sampaikan adalah benar dan dapat dipertanggungjawabkan secara hukum.
                </label>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-md rounded-3 py-2 fw-bold shadow-sm" id="btnSubmitSurat" disabled>
                  <i class="bi bi-send-fill me-2"></i> Kirim Pengajuan Surat
                </button>
                <small class="text-muted text-center" id="submitHint">Verifikasi nomor WhatsApp terlebih dahulu untuk mengaktifkan tombol kirim.</small>
                <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary btn-md rounded-3 py-2">
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

    function showErrorIn(el, message) {
      el.textContent = message;
      el.classList.remove('d-none');
    }

    function clearErrorIn(el) {
      el.classList.add('d-none');
      el.textContent = '';
    }

    // Reusable resend-cooldown countdown — dipakai baik oleh widget OTP
    // bawah (jalur NIK belum terdaftar) maupun widget OTP ringkas (jalur
    // NIK sudah terdaftar), supaya logikanya tidak di-fork dua kali.
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

    var btnSubmit = document.getElementById('btnSubmitSurat');
    var submitHint = document.getElementById('submitHint');
    var restOfForm = document.getElementById('restOfForm');
    var sectionOtpBottom = document.getElementById('sectionOtpBottom');
    var nikInput = document.getElementById('nik');

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

      if (nik.length !== 16) {
        showErrorIn(cekNikError, 'NIK harus 16 digit angka.');
        return;
      }

      triggerBtn.disabled = true;
      postJson('{{ route('otp.request') }}', { nik: nik }).then(function (result) {
        triggerBtn.disabled = false;

        if (!result.json.success) {
          showErrorIn(cekNikError, firstErrorMessage(result.json));
          return;
        }

        // Kunci NIK ke nilai yang sudah dicek, apa pun hasilnya (found atau tidak).
        nikInput.readOnly = true;
        cekNikStep.classList.add('d-none');

        if (result.json.data.found) {
          foundOtpStep.classList.remove('d-none');
          document.getElementById('foundOtpSentInfo').innerHTML =
            '<i class="bi bi-whatsapp me-1"></i> Kode OTP telah dikirim ke nomor <strong>' +
            result.json.data.phone_masked + '</strong>. Berlaku 5 menit.';
          startFoundCountdown(60);
        } else {
          // NIK belum terdaftar: lanjutkan alur lama — identitas kosong &
          // bisa diisi manual, verifikasi OTP di widget bawah sebelum submit.
          restOfForm.classList.remove('d-none');
          sectionOtpBottom.classList.remove('d-none');
        }
      }).catch(function () {
        triggerBtn.disabled = false;
        showErrorIn(cekNikError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    }

    btnCekNik.addEventListener('click', function () { sendNikCheck(btnCekNik); });

    btnFoundVerify.addEventListener('click', function () {
      clearErrorIn(foundOtpError);
      var code = foundOtpCode.value.trim();

      if (code.length !== 6) {
        showErrorIn(foundOtpError, 'Masukkan 6 digit kode OTP yang Anda terima.');
        return;
      }

      btnFoundVerify.disabled = true;
      postJson('{{ route('otp.verify') }}', { nik: nikInput.value.trim(), code: code }).then(function (result) {
        btnFoundVerify.disabled = false;

        if (!result.json.success) {
          showErrorIn(foundOtpError, firstErrorMessage(result.json));
          return;
        }

        // Auto-fill hanya setelah identitas terbukti (OTP lolos) — data
        // pemohon baru sekarang datang dari server, bukan diketik pengunjung.
        var pemohon = (result.json.data && result.json.data.pemohon) || {};
        document.getElementById('nama').value = pemohon.name || '';
        document.getElementById('phone').value = pemohon.phone || '';
        document.getElementById('alamat').value = pemohon.alamat || '';
        if (pemohon.kelurahan_id) {
          document.getElementById('kelurahan_id').value = pemohon.kelurahan_id;
        }
        document.getElementById('phone').readOnly = true;

        foundOtpStep.classList.add('d-none');
        restOfForm.classList.remove('d-none');
        // sectionOtpBottom sengaja TIDAK dibuka di jalur ini — sudah terverifikasi.
        submitHint.classList.add('d-none');
        btnSubmit.disabled = false;
      }).catch(function () {
        btnFoundVerify.disabled = false;
        showErrorIn(foundOtpError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    });

    btnFoundResend.addEventListener('click', function () { sendNikCheck(btnFoundResend); });

    // --- Widget OTP bawah (jalur NIK belum terdaftar) — tidak berubah -----
    var btnRequest = document.getElementById('btnRequestOtp');
    var btnVerify = document.getElementById('btnVerifyOtp');
    var btnResend = document.getElementById('btnResendOtp');
    var otpError = document.getElementById('otpError');
    var startCountdown = makeCountdown(btnResend, document.getElementById('resendCountdown'));

    function identityPayload() {
      return {
        nik: nikInput.value.trim(),
        phone: document.getElementById('phone').value.trim()
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
        startCountdown(60);
      }).catch(function () {
        btn.disabled = false;
        showErrorIn(otpError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    }

    btnRequest.addEventListener('click', function () { requestOtp(btnRequest); });
    btnResend.addEventListener('click', function () { requestOtp(btnResend); });

    btnVerify.addEventListener('click', function () {
      clearErrorIn(otpError);
      var payload = identityPayload();
      payload.code = document.getElementById('otpCode').value.trim();

      if (payload.code.length !== 6) {
        showErrorIn(otpError, 'Masukkan 6 digit kode OTP yang Anda terima.');
        return;
      }

      btnVerify.disabled = true;
      postJson('{{ route('otp.verify') }}', payload).then(function (result) {
        btnVerify.disabled = false;
        if (!result.json.success) {
          showErrorIn(otpError, firstErrorMessage(result.json));
          return;
        }
        document.getElementById('otpStepVerify').classList.add('d-none');
        document.getElementById('otpStepDone').classList.remove('d-none');
        submitHint.classList.add('d-none');
        btnSubmit.disabled = false;
        nikInput.readOnly = true;
        document.getElementById('phone').readOnly = true;
      }).catch(function () {
        btnVerify.disabled = false;
        showErrorIn(otpError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    });
  })();
</script>
@endpush
