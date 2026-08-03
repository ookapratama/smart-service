@extends('home.layouts.app')

@section('title', 'Masuk Warga - ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('body-class', 'starter-page-page')

@section('content')
<section class="section py-5" style="min-height: 65vh;">
  <div class="container" data-aos="fade-up">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-7">

        <div class="text-center mb-4 mt-4">
          <h2 class="fw-bold">Masuk Warga</h2>
          <p class="text-muted mb-0">
            Masuk cukup dengan NIK — kode OTP dikirim ke nomor WhatsApp yang
            tersimpan saat Anda pertama kali menggunakan layanan.
          </p>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body p-4">

            {{-- Step 1: NIK --}}
            <div id="stepNik">
              <label for="loginNik" class="form-label fw-semibold">NIK (16 digit)</label>
              <input type="text" class="form-control form-control-lg" id="loginNik"
                maxlength="16" inputmode="numeric" placeholder="16 digit NIK sesuai KTP" autocomplete="off">
              <div class="text-danger small mt-2 d-none" id="nikError"></div>
              <button type="button" class="btn btn-primary w-100 mt-3" id="btnKirimOtp">
                <i class="bi bi-whatsapp me-1"></i> Kirim Kode OTP
              </button>
            </div>

            {{-- Step 2: OTP --}}
            <div id="stepOtp" class="d-none">
              <div class="alert alert-info py-2 small" id="otpSentInfo">
                Jika NIK terdaftar, kode OTP telah dikirim ke nomor WhatsApp yang tersimpan. Berlaku 5 menit.
              </div>
              <label for="loginOtp" class="form-label fw-semibold">Kode OTP (6 digit)</label>
              <input type="text" class="form-control form-control-lg text-center" id="loginOtp"
                maxlength="6" inputmode="numeric" placeholder="______" autocomplete="one-time-code">
              <div class="text-danger small mt-2 d-none" id="otpError"></div>
              <button type="button" class="btn btn-primary w-100 mt-3" id="btnVerifikasi">
                <i class="bi bi-box-arrow-in-right me-1"></i> Verifikasi & Masuk
              </button>
              <div class="text-center mt-3">
                <button type="button" class="btn btn-link btn-sm text-decoration-none" id="btnKirimUlang" disabled>
                  Kirim ulang kode (<span id="resendCountdown">60</span>)
                </button>
              </div>
            </div>

          </div>
        </div>

        <p class="text-center text-muted small mt-3 mb-5">
          Belum pernah menggunakan layanan? Ajukan
          <a href="{{ route('surat.index') }}">surat online</a> atau
          <a href="{{ route('pengaduan.create') }}">pengaduan</a> terlebih dahulu —
          akun warga dibuat otomatis saat verifikasi WhatsApp pertama.
        </p>

      </div>
    </div>
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

    // Hanya terisi saat APP_DEBUG + driver WA `log` (lihat OtpService) — testing tanpa gateway.
    function logDebugCode(json) {
      if (json.data && json.data.debug_code) {
        console.log('[DEV] Kode OTP:', json.data.debug_code);
      }
    }

    function showError(el, message) {
      el.textContent = message;
      el.classList.remove('d-none');
    }

    function clearError(el) {
      el.classList.add('d-none');
      el.textContent = '';
    }

    var stepNik = document.getElementById('stepNik');
    var stepOtp = document.getElementById('stepOtp');
    var nikInput = document.getElementById('loginNik');
    var otpInput = document.getElementById('loginOtp');
    var nikError = document.getElementById('nikError');
    var otpError = document.getElementById('otpError');
    var btnKirim = document.getElementById('btnKirimOtp');
    var btnVerifikasi = document.getElementById('btnVerifikasi');
    var btnKirimUlang = document.getElementById('btnKirimUlang');
    var countdownSpan = document.getElementById('resendCountdown');
    var countdownTimer = null;

    // Respons request OTP sengaja generik (anti-enumeration) — countdown
    // kirim-ulang berjalan tetap 60 detik di sisi client.
    function startCountdown(seconds) {
      var left = seconds;
      btnKirimUlang.disabled = true;
      countdownSpan.textContent = left;
      clearInterval(countdownTimer);
      countdownTimer = setInterval(function () {
        left--;
        countdownSpan.textContent = left;
        if (left <= 0) {
          clearInterval(countdownTimer);
          btnKirimUlang.disabled = false;
          btnKirimUlang.textContent = 'Kirim ulang kode';
        }
      }, 1000);
    }

    function kirimOtp(triggerBtn) {
      clearError(nikError);
      clearError(otpError);
      var nik = nikInput.value.trim();

      if (nik.length !== 16 || !/^\d+$/.test(nik)) {
        showError(nikError, 'NIK harus 16 digit angka.');
        stepNik.classList.remove('d-none');
        return;
      }

      triggerBtn.disabled = true;
      postJson('{{ route('warga.login.otp') }}', { nik: nik }).then(function (result) {
        triggerBtn.disabled = false;

        if (!result.json.success) {
          showError(stepOtp.classList.contains('d-none') ? nikError : otpError, firstErrorMessage(result.json));
          return;
        }

        logDebugCode(result.json);
        stepNik.classList.add('d-none');
        stepOtp.classList.remove('d-none');
        otpInput.focus();
        startCountdown(60);
      }).catch(function () {
        triggerBtn.disabled = false;
        showError(nikError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    }

    btnKirim.addEventListener('click', function () { kirimOtp(btnKirim); });
    btnKirimUlang.addEventListener('click', function () { kirimOtp(btnKirimUlang); });

    btnVerifikasi.addEventListener('click', function () {
      clearError(otpError);
      var code = otpInput.value.trim();

      if (code.length !== 6) {
        showError(otpError, 'Masukkan 6 digit kode OTP yang Anda terima.');
        return;
      }

      btnVerifikasi.disabled = true;
      postJson('{{ route('warga.login.verify') }}', {
        nik: nikInput.value.trim(),
        code: code
      }).then(function (result) {
        btnVerifikasi.disabled = false;

        if (!result.json.success) {
          showError(otpError, firstErrorMessage(result.json));
          return;
        }

        window.location.href = (result.json.data && result.json.data.redirect) || '{{ route('tiket-saya.index') }}';
      }).catch(function () {
        btnVerifikasi.disabled = false;
        showError(otpError, 'Gagal menghubungi server. Periksa koneksi Anda.');
      });
    });

    nikInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); kirimOtp(btnKirim); }
    });
    otpInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); btnVerifikasi.click(); }
    });
  })();
</script>
@endpush
