@php
   $configData = Helper::appClasses();
   $customizerHidden = 'customizer-hide';
   $pageConfigs = ['myLayout' => 'blank'];
   $showEmailStage = old('email') || $errors->has('email') || $errors->has('password');
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('page-style')
   @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
   <div class="position-relative">
      <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
         <div class="authentication-inner py-6">

            <!-- Login -->
            <div class="card p-md-7 p-1">
               <!-- Logo -->
               <div class="app-brand justify-content-center mt-5">
                  <a href="{{ url('/') }}" class="app-brand-link gap-2">
                     <span class="app-brand-logo demo">
                        @if (get_setting('app_logo'))
                           <img src="{{ asset('storage/' . get_setting('app_logo')) }}" alt="Logo" height="30">
                        @else
                           @include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])
                        @endif
                     </span>
                     <span
                        class="app-brand-text demo text-heading fw-semibold">{{ get_setting('app_name', config('variables.templateName')) }}</span>
                  </a>
               </div>
               <!-- /Logo -->

               <div class="card-body mt-1">
                  <h4 class="mb-1">Masuk ke {{ get_setting('app_name', config('variables.templateName')) }}</h4>
                  <p class="mb-5">Staf gunakan email, warga gunakan NIK.</p>

                  {{-- Stage A: identifier --}}
                  <div id="stepIdentifier" class="{{ $showEmailStage ? 'd-none' : '' }}">
                     <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="identifier" placeholder="Email atau NIK"
                           {{ $showEmailStage ? '' : 'autofocus' }}>
                        <label for="identifier">Email (staf) atau NIK (warga)</label>
                     </div>
                     <div class="text-danger small mb-3 d-none" id="identifierError"></div>
                     <button type="button" class="btn btn-primary d-grid w-100" id="btnLanjut">Lanjut</button>
                  </div>

                  {{-- Stage B: staff email + password --}}
                  <div id="stepPassword" class="{{ $showEmailStage ? '' : 'd-none' }}">
                     <form id="formAuthentication" action="{{ route('login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" id="emailField" value="{{ old('email') }}">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                           <span class="fw-semibold" id="emailDisplay">{{ old('email') }}</span>
                           <button type="button" class="btn btn-link btn-sm p-0" id="btnGantiEmail">Ganti</button>
                        </div>
                        <div class="mb-3">
                           <div class="form-password-toggle">
                              <div class="input-group input-group-merge">
                                 <div class="form-floating form-floating-outline">
                                    <input type="password" id="password" class="form-control" name="password"
                                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                       aria-describedby="password" {{ $showEmailStage ? 'autofocus' : '' }} />
                                    <label for="password">Password</label>
                                 </div>
                                 <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                              </div>
                              @error('password')
                                 <span class="text-danger small">{{ $message }}</span>
                              @enderror
                           </div>
                        </div>
                        @error('email')
                           <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror
                        <div class="mb-3">
                           <div class="form-check">
                              <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                              <label class="form-check-label" for="remember-me">Ingat saya</label>
                           </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                     </form>

                     @if (get_setting('allow_registration', '1') === '1')
                        <p class="text-center mt-4">
                           <span>Belum punya akun?</span>
                           <a href="{{ route('register') }}">
                              <span>Daftar sekarang</span>
                           </a>
                        </p>
                     @endif
                  </div>

                  {{-- Stage B: warga NIK + OTP --}}
                  <div id="stepOtp" class="d-none">
                     <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold" id="nikDisplay"></span>
                        <button type="button" class="btn btn-link btn-sm p-0" id="btnGantiNik">Ganti</button>
                     </div>
                     <div class="alert alert-info py-2 small" id="otpSentInfo">
                        Jika NIK terdaftar, kode OTP telah dikirim ke nomor WhatsApp yang tersimpan. Berlaku 5
                        menit.
                     </div>
                     <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control text-center" id="otpCode" maxlength="6"
                           inputmode="numeric" placeholder="______" autocomplete="one-time-code">
                        <label for="otpCode">Kode OTP (6 digit)</label>
                     </div>
                     <div class="text-danger small mb-3 d-none" id="otpError"></div>
                     <button type="button" class="btn btn-primary d-grid w-100" id="btnVerifikasi">Verifikasi &
                        Masuk</button>
                     <div class="text-center mt-3">
                        <button type="button" class="btn btn-link btn-sm text-decoration-none" id="btnKirimUlang"
                           disabled>
                           Kirim ulang kode (<span id="resendCountdown">60</span>)
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <!-- /Login -->
         </div>
      </div>
   </div>
@endsection

@section('page-script')
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

      // debug_code hanya terisi saat APP_DEBUG + driver WA 'log' (dev/testing).
      function logDebugCode(json) {
         if (json.data && json.data.debug_code) {
            console.log('%c[DEV OTP] Kode OTP: ' + json.data.debug_code, 'background: #28a745; color: #ffffff; font-weight: bold; font-size: 14px; padding: 4px 8px; border-radius: 4px;');
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

      var stepIdentifier = document.getElementById('stepIdentifier');
      var stepPassword = document.getElementById('stepPassword');
      var stepOtp = document.getElementById('stepOtp');

      var identifierInput = document.getElementById('identifier');
      var identifierError = document.getElementById('identifierError');
      var btnLanjut = document.getElementById('btnLanjut');

      var emailField = document.getElementById('emailField');
      var emailDisplay = document.getElementById('emailDisplay');
      var passwordInput = document.getElementById('password');
      var btnGantiEmail = document.getElementById('btnGantiEmail');

      var nikDisplay = document.getElementById('nikDisplay');
      var otpInput = document.getElementById('otpCode');
      var otpError = document.getElementById('otpError');
      var btnVerifikasi = document.getElementById('btnVerifikasi');
      var btnKirimUlang = document.getElementById('btnKirimUlang');
      var countdownSpan = document.getElementById('resendCountdown');
      var btnGantiNik = document.getElementById('btnGantiNik');
      var countdownTimer = null;
      var currentNik = '';

      function showStage(stage) {
         stepIdentifier.classList.toggle('d-none', stage !== 'identifier');
         stepPassword.classList.toggle('d-none', stage !== 'password');
         stepOtp.classList.toggle('d-none', stage !== 'otp');
      }

      function resetToIdentifier() {
         clearInterval(countdownTimer);
         identifierInput.value = '';
         clearError(identifierError);
         showStage('identifier');
         identifierInput.focus();
      }

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
            }
         }, 1000);
      }

      function kirimOtp(nik, triggerBtn) {
         triggerBtn.disabled = true;
         postJson('{{ route('warga.login.otp') }}', { nik: nik }).then(function (result) {
            triggerBtn.disabled = false;

            if (!result.json.success) {
               showError(identifierError, firstErrorMessage(result.json));
               showStage('identifier');
               return;
            }

            logDebugCode(result.json);
            currentNik = nik;
            nikDisplay.textContent = 'NIK: ' + nik;
            otpInput.value = '';
            clearError(otpError);
            showStage('otp');
            otpInput.focus();
            startCountdown(60);
         }).catch(function () {
            triggerBtn.disabled = false;
            showError(identifierError, 'Gagal menghubungi server. Periksa koneksi Anda.');
            showStage('identifier');
         });
      }

      function lanjut() {
         clearError(identifierError);
         var value = identifierInput.value.trim();

         if (value.indexOf('@') !== -1) {
            emailField.value = value;
            emailDisplay.textContent = value;
            passwordInput.value = '';
            showStage('password');
            passwordInput.focus();
            return;
         }

         if (/^\d{16}$/.test(value)) {
            kirimOtp(value, btnLanjut);
            return;
         }

         showError(identifierError, 'Masukkan email staf atau NIK 16 digit warga.');
      }

      btnLanjut.addEventListener('click', lanjut);
      identifierInput.addEventListener('keydown', function (e) {
         if (e.key === 'Enter') { e.preventDefault(); lanjut(); }
      });

      btnGantiEmail.addEventListener('click', resetToIdentifier);
      btnGantiNik.addEventListener('click', resetToIdentifier);

      btnKirimUlang.addEventListener('click', function () { kirimOtp(currentNik, btnKirimUlang); });

      btnVerifikasi.addEventListener('click', function () {
         clearError(otpError);
         var code = otpInput.value.trim();

         if (code.length !== 6) {
            showError(otpError, 'Masukkan 6 digit kode OTP yang Anda terima.');
            return;
         }

         btnVerifikasi.disabled = true;
         postJson('{{ route('warga.login.verify') }}', {
            nik: currentNik,
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

      otpInput.addEventListener('keydown', function (e) {
         if (e.key === 'Enter') { e.preventDefault(); btnVerifikasi.click(); }
      });
   })();
</script>
@endsection
