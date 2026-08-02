@extends('layouts/layoutMaster')

@section('title', 'Website Settings')

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
                           <i class="ri-building-line me-1"></i> Profil Kecamatan
                        </button>
                     </li>
                     @endif
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-contact" aria-controls="navs-contact" aria-selected="false">
                           <i class="ri-contacts-line me-1"></i> Kontak & Sosmed
                        </button>
                     </li>
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-system" aria-controls="navs-system" aria-selected="false">
                           <i class="ri-settings-4-line me-1"></i> Sistem
                        </button>
                     </li>
                     @if(isset($groupedSettings['whatsapp']) || isset($groupedSettings['tiket']))
                     <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                           data-bs-target="#navs-integrasi" aria-controls="navs-integrasi" aria-selected="false">
                           <i class="ri-whatsapp-line me-1"></i> WA Gateway & Tiket
                        </button>
                     </li>
                     @endif
                  </ul>
               </div>
               <div class="card-body">
                  <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                     <div class="tab-content p-0">
                        {{-- General Settings --}}
                        <div class="tab-pane fade show active" id="navs-general" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['general'] ?? [] as $setting)
                                 <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    @if ($setting->type === 'text')
                                       <input type="text" name="{{ $setting->key }}" class="form-control"
                                          value="{{ $setting->value }}">
                                    @elseif($setting->type === 'textarea')
                                       <textarea name="{{ $setting->key }}" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                    @elseif($setting->type === 'color')
                                       <input type="color" name="{{ $setting->key }}" class="form-control form-control-color w-100"
                                          value="{{ $setting->value ?? '#0d6efd' }}">
                                    @elseif($setting->type === 'image')
                                       <div class="d-flex align-items-start align-items-sm-center gap-4">
                                          @if ($setting->value)
                                             <img src="{{ asset('storage/' . $setting->value) }}" alt="image"
                                                class="d-block rounded border p-1" height="60" width="60" style="object-fit: contain;">
                                          @else
                                             <div
                                                class="bg-label-secondary rounded d-flex align-items-center justify-content-center"
                                                style="height: 60px; width: 60px;">
                                                <i class="ri-image-line ri-24px"></i>
                                             </div>
                                          @endif
                                          <div class="button-wrapper">
                                             <input type="file" name="{{ $setting->key }}"
                                                class="form-control form-control-sm">
                                             <div class="text-muted small mt-1">Format JPG, PNG, atau SVG. Maks 1MB.</div>
                                          </div>
                                       </div>
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
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    @if ($setting->type === 'text')
                                       <input type="text" name="{{ $setting->key }}" class="form-control"
                                          value="{{ $setting->value }}" placeholder="Masukkan {{ strtolower($setting->label) }}">
                                    @elseif($setting->type === 'textarea')
                                       <textarea name="{{ $setting->key }}" class="form-control" rows="3" placeholder="Masukkan {{ strtolower($setting->label) }}">{{ $setting->value }}</textarea>
                                    @elseif($setting->type === 'image')
                                       <div class="d-flex align-items-start align-items-sm-center gap-4">
                                          @if ($setting->value)
                                             <img src="{{ asset('storage/' . $setting->value) }}" alt="banner image"
                                                class="d-block rounded border p-1" height="70" width="100" style="object-fit: cover;">
                                          @else
                                             <div
                                                class="bg-label-secondary rounded d-flex align-items-center justify-content-center"
                                                style="height: 70px; width: 100px;">
                                                <i class="ri-image-line ri-24px"></i>
                                             </div>
                                          @endif
                                          <div class="button-wrapper">
                                             <input type="file" name="{{ $setting->key }}"
                                                class="form-control form-control-sm">
                                             <div class="text-muted small mt-1">Format JPG, PNG, WEBP. Maks 2MB.</div>
                                          </div>
                                       </div>
                                    @endif
                                 </div>
                              @endforeach
                           </div>
                        </div>
                        @endif

                        {{-- Profil Kecamatan Settings --}}
                        @if(isset($groupedSettings['profil']))
                        <div class="tab-pane fade" id="navs-profil" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['profil'] as $setting)
                                 <div class="{{ $setting->type === 'textarea' ? 'col-md-12' : 'col-md-6' }}">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    @if ($setting->type === 'textarea')
                                       <textarea name="{{ $setting->key }}" class="form-control" rows="3" placeholder="Masukkan {{ strtolower($setting->label) }}">{{ $setting->value }}</textarea>
                                    @else
                                       <input type="text" name="{{ $setting->key }}" class="form-control"
                                          value="{{ $setting->value }}" placeholder="Masukkan {{ strtolower($setting->label) }}">
                                    @endif
                                 </div>
                              @endforeach
                           </div>
                        </div>
                        @endif

                        {{-- Contact Settings --}}
                        <div class="tab-pane fade" id="navs-contact" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['contact'] ?? [] as $setting)
                                 <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    <div class="input-group input-group-merge">
                                       <span class="input-group-text">
                                          <i class="{{ str_contains($setting->key, 'email') ? 'ri-mail-line' : (str_contains($setting->key, 'phone') ? 'ri-whatsapp-line' : (str_contains($setting->key, 'facebook') ? 'ri-facebook-line' : (str_contains($setting->key, 'youtube') ? 'ri-youtube-line' : 'ri-instagram-line'))) }}"></i>
                                       </span>
                                       <input type="text" name="{{ $setting->key }}" class="form-control"
                                          value="{{ $setting->value }}" placeholder="Masukkan {{ strtolower($setting->label) }}">
                                    </div>
                                 </div>
                              @endforeach
                           </div>
                        </div>

                        {{-- System Settings --}}
                        <div class="tab-pane fade" id="navs-system" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['system'] ?? [] as $setting)
                                 <div class="col-md-6">
                                    <div class="card border shadow-none p-3">
                                       <div class="form-check form-switch m-0">
                                          <input type="hidden" name="{{ $setting->key }}" value="0">
                                          <input class="form-check-input" type="checkbox" name="{{ $setting->key }}"
                                             id="{{ $setting->key }}" value="1"
                                             {{ $setting->value == '1' ? 'checked' : '' }}>
                                          <label class="form-check-label fw-bold"
                                             for="{{ $setting->key }}">{{ $setting->label }}</label>
                                       </div>
                                       <small class="text-muted d-block mt-2">Mengaktifkan fitur ini akan berdampak langsung pada akses masyarakat di halaman publik.</small>
                                    </div>
                                 </div>
                              @endforeach
                           </div>
                        </div>

                        {{-- WhatsApp Gateway & Tiket Settings --}}
                        @if(isset($groupedSettings['whatsapp']) || isset($groupedSettings['tiket']))
                        <div class="tab-pane fade" id="navs-integrasi" role="tabpanel">
                           <div class="row g-4 mt-1">
                              @foreach ($groupedSettings['whatsapp'] ?? [] as $setting)
                                 <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    <input type="text" name="{{ $setting->key }}" class="form-control"
                                       value="{{ $setting->value }}" placeholder="Masukkan {{ strtolower($setting->label) }}">
                                 </div>
                              @endforeach
                              @foreach ($groupedSettings['tiket'] ?? [] as $setting)
                                 <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    <input type="text" name="{{ $setting->key }}" class="form-control"
                                       value="{{ $setting->value }}" placeholder="Masukkan {{ strtolower($setting->label) }}">
                                 </div>
                              @endforeach
                           </div>
                        </div>
                        @endif
                     </div>

                     <div class="mt-4 border-top pt-4">
                        <button type="submit" class="btn btn-primary me-2">
                           <i class="ri-save-line me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('settings.clear-cache') }}" class="btn btn-outline-warning me-2">
                           <i class="ri-refresh-line me-1"></i> Bersihkan Cache
                        </a>
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                     </div>
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
