@extends('layouts/layoutMaster')

@section('title', 'Website Settings')

@php
   $selectOptions = [
      'wa_driver' => ['log' => 'Log (testing — kode hanya dicatat di log)', 'whatsapp_web_js' => 'WhatsApp Web JS Gateway'],
   ];
   $iconMap = [
      'profile_telepon' => 'ri-phone-line',
      'profile_email' => 'ri-mail-line',
      'contact_phone' => 'ri-whatsapp-line',
      'social_instagram' => 'ri-instagram-line',
      'social_facebook' => 'ri-facebook-line',
      'social_youtube' => 'ri-youtube-line',
   ];
   $systemHelp = [
      'maintenance_mode' => 'Saat aktif, seluruh halaman publik menampilkan halaman perawatan. Admin tetap bisa login.',
      'allow_registration' => 'Mengizinkan pendaftaran akun dashboard baru lewat halaman register. Tidak berkaitan dengan login warga.',
   ];
@endphp

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">
         <span class="text-muted fw-light">Sistem /</span> Pengaturan Website
      </h4>

      <div class="row">
         <div class="col-md-12">
            <div class="card mb-4">
               <div class="card-header border-bottom">
                  <ul class="nav nav-pills card-header-pills" role="tablist">
                     <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-general" aria-controls="navs-general" aria-selected="true">
                           <i class="ri-global-line me-1"></i> Umum
                        </button>
                     </li>
                     @if(isset($groupedSettings['banner']))
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-banner" aria-controls="navs-banner" aria-selected="false">
                           <i class="ri-image-edit-line me-1"></i> Banner & Hero
                        </button>
                     </li>
                     @endif
                     @if(isset($groupedSettings['profil']))
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-profil" aria-controls="navs-profil" aria-selected="false">
                           <i class="ri-building-line me-1"></i> Profil & Kontak
                        </button>
                     </li>
                     @endif
                     @if(isset($groupedSettings['system']))
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-system" aria-controls="navs-system" aria-selected="false">
                           <i class="ri-settings-4-line me-1"></i> Sistem
                        </button>
                     </li>
                     @endif
                     @if(isset($groupedSettings['penandatangan']))
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-penandatangan" aria-controls="navs-penandatangan" aria-selected="false">
                           <i class="ri-quill-pen-line me-1"></i> Penandatangan Surat
                        </button>
                     </li>
                     @endif
                     @if(isset($groupedSettings['whatsapp']) || isset($groupedSettings['tiket']))
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-integrasi" aria-controls="navs-integrasi" aria-selected="false">
                           <i class="ri-plug-line me-1"></i> Integrasi
                        </button>
                     </li>
                     @endif
                  </ul>
               </div>
               <div class="card-body">
                  @if ($errors->any())
                     <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-1">Beberapa isian belum valid:</div>
                        <ul class="mb-0 ps-3">
                           @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                  @endif

                  <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                     <div class="tab-content p-0">
                        {{-- General Settings --}}
                        <div class="tab-pane fade show active" id="navs-general" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['general'] ?? [] as $setting)
                                 <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="{{ $setting->key }}">{{ $setting->label }}</label>
                                    @if ($setting->type === 'textarea')
                                       <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3"
                                          class="form-control @error($setting->key) is-invalid @enderror">{{ old($setting->key, $setting->value) }}</textarea>
                                    @elseif($setting->type === 'image')
                                       <div class="d-flex align-items-start align-items-sm-center gap-4">
                                          @if ($setting->value)
                                             <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}"
                                                class="d-block rounded border p-1" height="60" width="60" style="object-fit: contain;">
                                          @else
                                             <div class="bg-label-secondary rounded d-flex align-items-center justify-content-center"
                                                style="height: 60px; width: 60px;">
                                                <i class="ri-image-line ri-24px"></i>
                                             </div>
                                          @endif
                                          <div class="button-wrapper">
                                             <input type="file" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="form-control form-control-sm @error($setting->key) is-invalid @enderror">
                                             <div class="text-muted small mt-1">Format JPG, PNG, atau SVG. Maks 1MB.</div>
                                             @error($setting->key)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                             @enderror
                                          </div>
                                       </div>
                                    @else
                                       <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                          class="form-control @error($setting->key) is-invalid @enderror"
                                          value="{{ old($setting->key, $setting->value) }}">
                                    @endif
                                    @if ($setting->type !== 'image')
                                       @error($setting->key)
                                          <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                    @endif
                                 </div>
                              @endforeach
                           </div>
                        </div>

                        {{-- Banner & Hero Settings --}}
                        @if(isset($groupedSettings['banner']))
                        <div class="tab-pane fade" id="navs-banner" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['banner'] as $setting)
                                 <div class="{{ $setting->type === 'textarea' ? 'col-md-12' : 'col-md-6' }}">
                                    <label class="form-label fw-semibold" for="{{ $setting->key }}">{{ $setting->label }}</label>
                                    @if ($setting->type === 'textarea')
                                       <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3"
                                          class="form-control @error($setting->key) is-invalid @enderror">{{ old($setting->key, $setting->value) }}</textarea>
                                    @elseif($setting->type === 'image')
                                       <div class="d-flex align-items-start align-items-sm-center gap-4">
                                          @if ($setting->value)
                                             <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}"
                                                class="d-block rounded border p-1" height="70" width="100" style="object-fit: cover;">
                                          @else
                                             <div class="bg-label-secondary rounded d-flex align-items-center justify-content-center"
                                                style="height: 70px; width: 100px;">
                                                <i class="ri-image-line ri-24px"></i>
                                             </div>
                                          @endif
                                          <div class="button-wrapper">
                                             <input type="file" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="form-control form-control-sm @error($setting->key) is-invalid @enderror">
                                             <div class="text-muted small mt-1">Format JPG, PNG, WEBP. Maks 2MB.</div>
                                             @error($setting->key)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                             @enderror
                                          </div>
                                       </div>
                                    @else
                                       <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                          class="form-control @error($setting->key) is-invalid @enderror"
                                          value="{{ old($setting->key, $setting->value) }}">
                                    @endif
                                    @if ($setting->type !== 'image')
                                       @error($setting->key)
                                          <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                    @endif
                                 </div>
                              @endforeach
                           </div>
                        </div>
                        @endif

                        {{-- Profil Kecamatan + Kontak & Sosmed --}}
                        @if(isset($groupedSettings['profil']))
                        <div class="tab-pane fade" id="navs-profil" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['profil'] as $setting)
                                 <div class="{{ $setting->type === 'textarea' ? 'col-md-12' : 'col-md-6' }}">
                                    <label class="form-label fw-semibold" for="{{ $setting->key }}">{{ $setting->label }}</label>
                                    @if ($setting->type === 'textarea')
                                       <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3"
                                          class="form-control @error($setting->key) is-invalid @enderror">{{ old($setting->key, $setting->value) }}</textarea>
                                    @elseif (isset($iconMap[$setting->key]))
                                       <div class="input-group input-group-merge">
                                          <span class="input-group-text"><i class="{{ $iconMap[$setting->key] }}"></i></span>
                                          <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                             class="form-control @error($setting->key) is-invalid @enderror"
                                             value="{{ old($setting->key, $setting->value) }}">
                                       </div>
                                    @else
                                       <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                          class="form-control @error($setting->key) is-invalid @enderror"
                                          value="{{ old($setting->key, $setting->value) }}">
                                    @endif
                                    @error($setting->key)
                                       <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                 </div>
                              @endforeach
                           </div>
                        </div>
                        @endif

                        {{-- System Settings --}}
                        @if(isset($groupedSettings['system']))
                        <div class="tab-pane fade" id="navs-system" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['system'] as $setting)
                                 <div class="col-md-6">
                                    <div class="card border shadow-none p-3">
                                       <div class="form-check form-switch m-0">
                                          <input type="hidden" name="{{ $setting->key }}" value="0">
                                          <input class="form-check-input" type="checkbox" name="{{ $setting->key }}"
                                             id="{{ $setting->key }}" value="1"
                                             {{ old($setting->key, $setting->value) == '1' ? 'checked' : '' }}>
                                          <label class="form-check-label fw-bold"
                                             for="{{ $setting->key }}">{{ $setting->label }}</label>
                                       </div>
                                       <small class="text-muted d-block mt-2">{{ $systemHelp[$setting->key] ?? '' }}</small>
                                    </div>
                                 </div>
                              @endforeach
                           </div>
                        </div>
                        @endif

                        {{-- Penandatangan Surat Resmi (blok ttd PDF) --}}
                        @if(isset($groupedSettings['penandatangan']))
                        <div class="tab-pane fade" id="navs-penandatangan" role="tabpanel">
                           <div class="alert alert-light border small mt-3 mb-1">
                              <i class="ri-information-line me-1"></i>
                              Data ini tampil pada blok tanda tangan di PDF surat resmi. Kosongkan nama/NIP untuk menampilkan garis titik-titik (diisi manual setelah dicetak).
                           </div>
                           <div class="row g-4 mt-0">
                              @foreach ($groupedSettings['penandatangan'] as $setting)
                                 <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="{{ $setting->key }}">{{ $setting->label }}</label>
                                    <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                       class="form-control @error($setting->key) is-invalid @enderror"
                                       value="{{ old($setting->key, $setting->value) }}">
                                    @error($setting->key)
                                       <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                 </div>
                              @endforeach
                           </div>
                        </div>
                        @endif

                        {{-- Integrasi: WhatsApp Gateway + Penomoran Tiket --}}
                        @if(isset($groupedSettings['whatsapp']) || isset($groupedSettings['tiket']))
                        <div class="tab-pane fade" id="navs-integrasi" role="tabpanel">
                           @if(isset($groupedSettings['whatsapp']))
                              <h6 class="fw-semibold mt-3 mb-0"><i class="ri-whatsapp-line me-1"></i> WhatsApp Gateway</h6>
                              <div class="row g-4 mt-0">
                                 @foreach ($groupedSettings['whatsapp'] as $setting)
                                    <div class="col-md-6">
                                       <label class="form-label fw-semibold" for="{{ $setting->key }}">{{ $setting->label }}</label>
                                       @if ($setting->type === 'select' && isset($selectOptions[$setting->key]))
                                          <select name="{{ $setting->key }}" id="{{ $setting->key }}"
                                             class="form-select @error($setting->key) is-invalid @enderror">
                                             @foreach ($selectOptions[$setting->key] as $optValue => $optLabel)
                                                <option value="{{ $optValue }}" {{ old($setting->key, $setting->value) === $optValue ? 'selected' : '' }}>
                                                   {{ $optLabel }}
                                                </option>
                                             @endforeach
                                          </select>
                                       @else
                                          <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                             class="form-control @error($setting->key) is-invalid @enderror"
                                             value="{{ old($setting->key, $setting->value) }}">
                                       @endif
                                       @error($setting->key)
                                          <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                    </div>
                                 @endforeach
                              </div>
                           @endif
                           @if(isset($groupedSettings['tiket']))
                              <h6 class="fw-semibold mt-4 mb-0"><i class="ri-coupon-2-line me-1"></i> Penomoran Tiket</h6>
                              <div class="row g-4 mt-0">
                                 @foreach ($groupedSettings['tiket'] as $setting)
                                    <div class="col-md-6">
                                       <label class="form-label fw-semibold" for="{{ $setting->key }}">{{ $setting->label }}</label>
                                       <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                          class="form-control @error($setting->key) is-invalid @enderror"
                                          value="{{ old($setting->key, $setting->value) }}">
                                       @error($setting->key)
                                          <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                    </div>
                                 @endforeach
                              </div>
                           @endif
                        </div>
                        @endif
                     </div>

                     <div class="mt-4 border-top pt-4 d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary">
                           <i class="ri-save-line me-1"></i> Simpan Perubahan
                        </button>
                     </div>
                  </form>

                  <form action="{{ route('settings.clear-cache') }}" method="POST" class="mt-3">
                     @csrf
                     <button type="submit" class="btn btn-outline-warning btn-sm">
                        <i class="ri-refresh-line me-1"></i> Bersihkan Cache Pengaturan
                     </button>
                  </form>
               </div>
            </div>

            {{-- Info Alert --}}
            <div class="alert alert-primary d-flex align-items-center mb-0" role="alert">
               <span class="alert-icon me-2">
                  <i class="ri-information-line ri-22px"></i>
               </span>
               <span>Perubahan pada pengaturan ini akan disimpan dan diterapkan secara instan ke seluruh tampilan Landing Page & portal publik melalui sistem Cache.</span>
            </div>
         </div>
      </div>
   </div>
@endsection
