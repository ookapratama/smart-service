@php
   $configData = Helper::appClasses();
   $customizerHidden = 'customizer-hide';
   $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('vendor-style')
   @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
   @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
   @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
   @vite(['resources/assets/js/pages-auth.js'])
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
                  <p class="mb-5">Gunakan email dan password staf untuk masuk.</p>

                  <form id="formAuthentication" class="mb-5" action="{{ route('login') }}" method="POST">
                     @csrf
                     <div class="form-floating form-floating-outline mb-5">
                        <input type="text" class="form-control" id="email" name="email"
                           value="{{ old('email') }}" placeholder="Masukkan email" autofocus>
                        <label for="email">Email</label>
                        @error('email')
                           <span class="text-danger small">{{ $message }}</span>
                        @enderror
                     </div>
                     <div class="mb-5">
                        <div class="form-password-toggle">
                           <div class="input-group input-group-merge">
                              <div class="form-floating form-floating-outline">
                                 <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                 <label for="password">Password</label>
                              </div>
                              <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                           </div>
                           @error('password')
                              <span class="text-danger small">{{ $message }}</span>
                           @enderror
                        </div>
                     </div>
                     <div class="mb-5">
                        <div class="form-check">
                           <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                           <label class="form-check-label" for="remember-me">
                              Ingat saya
                           </label>
                        </div>
                     </div>
                     <div class="mb-5">
                        <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                     </div>
                  </form>

                  @if (get_setting('allow_registration', '1') === '1')
                     <p class="text-center">
                        <span>Belum punya akun?</span>
                        <a href="{{ route('register') }}">
                           <span>Daftar sekarang</span>
                        </a>
                     </p>
                  @endif

                  <p class="text-center text-muted small mt-4 mb-0">
                     Warga? Masuk lewat <a href="{{ route('warga.login') }}">halaman Masuk Warga</a>.
                  </p>
               </div>
            </div>
            <!-- /Login -->
         </div>
      </div>
   </div>
@endsection
