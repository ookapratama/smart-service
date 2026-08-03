# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

-   **Template PDF per kategori + penandatangan dari settings**: template baru `surat/templates/keterangan.blade.php` (SKTM/SKU/SKBM/SKKL/SKKM/SKP — "menerangkan bahwa") dan `pengantar.blade.php` (SPKTP/SPKK/SPSKCK — "memberikan pengantar", kalimat yang sebelumnya salah karena semua jatuh ke generik). Blok tanda tangan jadi partial `partials/ttd.blade.php` yang membaca settings group baru `penandatangan` (`ttd_jabatan`/`ttd_nama`/`ttd_nip`, tab baru di Pengaturan Website) — hardcode "Camat Soreang"/ttd kosong dihapus. `jenis_surat.template_view` kini bisa dipilih dari form admin (select keterangan/pengantar/skd/generik) dan `SuratService::resolveTemplateView()` menerima nama pendek dengan guard `view()->exists()` → fallback generik. Data migration `2026_08_03_000400` mengisi `template_view` baris production yang masih null (idempotent, tidak menimpa pilihan admin).
-   **Editor field formulir di admin Jenis Surat**: form create/edit kini punya builder field dinamis (nama/label/tipe text-textarea-number-date-select-file/wajib/opsi) yang tersimpan ke `jenis_surat.fields` — sebelumnya `fields` sama sekali tidak bisa diisi dari admin (hanya seeder), sehingga jenis surat buatan admin selalu tanpa formulir. Validasi skema di `JenisSuratRequest` (nama field snake_case unik, select wajib beropsi). Lampiran pengantar RT/RW kini field `type:file` sungguhan di `fields` (seeder + data migration `2026_08_03_000300` untuk baris production, idempotent) — kolom `wajib_pengantar_rt_rw` deprecated (tetap ada, tidak dipakai form). `JenisSuratSeeder` diubah ke `firstOrCreate` supaya deploy tidak menimpa editan admin.
-   **Login warga passwordless by NIK (§4) + halaman Tiket Saya (§5.2)**: `GET /masuk` (`Landing\WargaAuthController`) — masukkan NIK → OTP 6 digit ke nomor WA tersimpan → login (`Auth::login` remember; cookie recaller memakai durasi default framework, bukan 30 hari — deviasi dicatat di sini). Akun `users` role `warga` (password null) dibuat implisit saat OTP pertama lolos dan di-link ke `pemohon.user_id`, sehingga riwayat tiket lama dengan NIK sama otomatis ikut. Endpoint login **anti-enumeration** (respons byte-identical untuk NIK terdaftar/tidak — sengaja terpisah dari wizard persuratan yang membuka `found`). `GET /tiket-saya` menampilkan seluruh tiket milik warga (pengaduan + surat) dengan timeline status dan unduh PDF surat jadi tanpa gerbang nomor+NIK (sesi login menggantikannya; kepemilikan dipagari `pemohon_id`, bukan milik → 404). Middleware baru `staff` (`EnsureStaffUser`) menutup celah warga membuka `/admin/*` (deny-list warga); redirect guest/user kini per-role (`bootstrap/app.php`), logout warga kembali ke landing. Header landing menampilkan status login + dropdown; seksi panduan "Segera Hadir" diganti panduan login riil.
-   **`debug_code` OTP untuk testing tanpa gateway WA**: respons sukses `POST /otp/request` menyertakan kode OTP 6 digit di field `data.debug_code` dan front-end menampilkannya di browser console (`[DEV] Kode OTP: ...`). Gerbang ganda di `OtpService::debugCodeEnabled()`: hanya saat `APP_DEBUG=true` DAN notifier terikat adalah driver `log` — tidak pernah bocor saat gateway asli aktif atau di production (ada regression test-nya).

-   **Petugas persuratan + PDF surat resmi (Fase C)**: new `SuratService` — race-safe yearly nomor surat (`{seq}/{kode}/KEC-SRG/{tahun}`, atomic `SuratCounter::upsert()` + `lockForUpdate()` read-back) assigned inside the status transaction, and dompdf rendering (`barryvdh/laravel-dompdf`) strictly after commit via per-jenis Blade templates (`resources/views/surat/templates/{kode}.blade.php`, SKD example + `generik` fallback, letterhead from `profile_*` settings). `TiketService::updateStatus()` now runs in a transaction and, for `pengajuan_surat` tickets reaching `selesai`, issues the nomor surat (idempotent) then generates the PDF (failure logs + warns, never rolls back; petugas can regenerate). PDF stored on the private `local` disk as media `collection` `surat_pdf`. Tiket detail page shows dynamic form data (labels from `jenis_surat.fields`), grouped lampiran with secure download links, nomor surat, PDF download + regenerate. New admin routes `tiket.lampiran`/`tiket.surat-pdf`/`tiket.regenerate-surat-pdf` (registered in `CheckPermission::$routeNameMap`); public download `GET /surat/unduh/{nomor_tiket}` gated by nomor tiket + NIK, surfaced on the sukses page and the cek-status result.
-   **Halaman panduan publik (Fase D)**: `GET /panduan` (`HomeController::panduan`, `resources/views/home/panduan.blade.php`) — plain-language, numbered how-to for pengaduan, pengajuan surat (incl. WhatsApp OTP + lampiran), cek status, upcoming NIK login ("segera hadir"), and a short FAQ. Linked from the landing header, footer, and mobile bottom nav.
-   **OTP session flag hardening**: the `otp_verified` session flag now carries a `purpose` (`persuratan`); persuratan submit rejects flags missing or mismatching it.

-   **OTP engine (WA driver `log`)**: interface `App\Contracts\Services\WhatsAppNotifier` + `LogWhatsAppNotifier`, bound in `AppServiceProvider` from the `wa_driver` setting (fallback `log`). `OtpService` implements `S3_MVP_DESIGN.md` §4: hashed 6-digit code in cache (TTL 5 min), max 5 attempts, 60s resend cooldown, per-NIK/per-IP throttling, anti-enumeration responses, and OTP delivery to the stored verified phone when the submitted number differs. Web AJAX endpoints `POST /otp/request` and `POST /otp/verify` (`OtpController`); successful verify stores a 15-minute `otp_verified` session flag. New migration adds `pemohon.phone_verified_at`.
-   **Public persuratan flow**: `PengajuanSuratPublicController` + `resources/views/home/surat/` (list of active jenis surat, dynamic form rendered from `jenis_surat.fields` JSON incl. per-jenis file requirements, in-page OTP verification, success page). Submit requires a matching OTP session flag, runs pemohon `updateOrCreate` + `pengajuan_surat` + counter + tiket + `status_log` in one transaction, then (after commit) stores attachments via `FileUploadService` (media `collection` = field name) and sends the ticket number over WhatsApp (logged to `notifikasi_wa`). "Buat Surat" added to the landing header and mobile bottom nav.

-   **Single-kecamatan schema redesign**: replaced the `instansi`/`wilayah` multi-tenant design with a single-kecamatan model targeting Kecamatan Soreang, Kota Parepare. New master table `kelurahan` (7 rows, seeded), new tables `agenda`, `galeri`, `notifikasi_wa`, `surat_counters` for upcoming MVP features. New role `warga` (passwordless, OTP-by-NIK login is a later phase) and `petugas` (renamed from `petugas-instansi`). New `DemoDataSeeder` (always seeded, idempotent) with sample tiket/pemohon spanning all status values. Full design doc: `S3_MVP_DESIGN.md`.
-   **Deploy webhook**: `POST /api/deploy/webhook` (`DeployWebhookController`) runs `migrate`/`db:seed`/cache/`queue:restart` on the production server, authenticated via a shared-secret token (`services.deploy.webhook_token`) instead of a login session.

### Changed

-   **Antrian tiket petugas di-upgrade**: paginasi server-side 20/halaman (sebelumnya memuat SEMUA baris), tab Semua/Persuratan/Pengaduan, pencarian nomor tiket / nama / NIK pemohon, filter jenis surat pada tab persuratan (`TiketRepository::filtered()` kini mengembalikan `LengthAwarePaginator`). Menolak tiket kini **wajib catatan** (`required_if:status_to,ditolak`) — warga tidak lagi menerima penolakan tanpa penjelasan.
-   **Admin Settings form dirapikan**: tab "Kontak & Sosmed" dilebur ke "Profil & Kontak" (duplikasi email/telepon dihapus), `wa_driver` menjadi select (`log`/`fonnte`) dengan validasi `in:`, pesan validasi tampil per field (`@error`), help text toggle Sistem diperbaiki, dan tombol "Bersihkan Cache" menjadi form POST (route `settings.clear-cache` berubah verb GET → POST).
-   **`SettingSeeder` kini value-preserving**: baris yang sudah ada hanya diperbarui metadata-nya (`group`/`type`/`label`) — sebelumnya `db:seed` (yang berjalan di setiap deploy) mereset nilai settings hasil edit admin ke default seeder. Migration `2026_08_02_140000` kini mendelegasikan ke seeder yang sama, menghilangkan daftar duplikat.
-   **`TiketService::generateNomorTiket()`**: nomor tiket generation moved to an atomic `TiketCounter::upsert()` + `lockForUpdate()` read-back, replacing a `first() ?: create()` pattern that race-lost when two requests hit the first request of a new periode simultaneously.
-   **`nik` and `nomor_tiket` are now globally unique** (previously scoped per-`instansi_id`).
-   **`tiket.pemohon_id` is `restrictOnDelete()`** (previously cascade) — deleting a pemohon no longer silently deletes their ticket history.
-   **CI/CD deploy pipeline**: switched from rsync-over-SSH to FTP (`SamKirkland/FTP-Deploy-Action`) for file upload, since GitHub Actions' runner IPs get their SSH connection reset on the target host. Post-deploy commands (`migrate`/`db:seed`/cache/`queue:restart`) moved from SSH (`appleboy/ssh-action`) to the new HTTP deploy webhook, triggered automatically at the end of every deploy. See `DEPLOYMENT.md` and `REFACTOR_BACKLOG.md` for details.

### Fixed

-   **Kolom `pengajuan_surat.keperluan` dilebarkan ke varchar(500)** (migration `2026_08_03_000200`) — sebelumnya varchar(255) sementara validasi mengizinkan `max:500`; nilai 256-500 karakter memicu `QueryException` di MySQL strict mode.
-   **Pesan WA update status menyebut jenis layanan yang benar**: sebelumnya semua tiket disebut "tiket pengaduan" (hardcode di `TiketService`); kini mengikuti `detail_type`, dan pengajuan surat yang selesai menyertakan tautan unduh langsung (nomor tiket + NIK milik penerima).
-   **`retry_after` cooldown OTP kini sisa detik sebenarnya**, bukan konstanta 60 — nilai cache cooldown menyimpan timestamp kedaluwarsa.
-   **Wizard persuratan pulih setelah submit gagal validasi**: flag OTP session yang masih berlaku dikirim balik ke halaman form (`create()` → `verifiedNik`) sehingga wizard langsung ke keadaan terverifikasi dan tombol kirim aktif — sebelumnya UI reset total padahal user sudah verifikasi.
-   **`pages/tiket/show.blade.php` memakai `detail_type` (morph alias)** menggantikan `str_contains(get_class(...))` yang rapuh.
-   **Mass-assignment di update settings**: `SettingController::update` memakai `$request->except()` atas seluruh input sehingga key POST liar menjadi baris `settings` baru; kini dibatasi `$request->safe()` (hanya key ber-rule di `SettingRequest`).
-   **JSON validation errors returned 500**: the global exception renderer in `bootstrap/app.php` converted `ValidationException` on JSON requests into a generic 500 (only FormRequests with `BaseRequest::failedValidation` escaped it). `ValidationException` now falls through to Laravel's built-in rendering (redirect + errors bag for web, 422 JSON for AJAX).
-   **`PengaduanPublicController::store()`** stored `detail_type` as an FQCN instead of the morph-map alias, breaking `whereHasMorph()` queries against publicly-submitted pengaduan. Now creates the tiket via `$pengaduan->tiket()->create([...])`, matching the morph-map alias convention used elsewhere.
-   **`LandingApiController::cekStatus()`**: `where('nomor_tiket', ...)->orWhereHas(...)` was unparenthesized, so any future added condition on the query would have silently leaked past the intended filter. Wrapped in a closure.
-   Typo "Sorean" → "Soreang" throughout landing pages, seeders, and controller copy; incorrect Kab. Bandung address/phone/postal-code placeholders corrected to Kota Parepare.

### Removed

-   **Settings mati dihapus** (migration `2026_08_03_000100_remove_dead_settings`): `profile_map_embed`, `profile_kode_wilayah`, `contact_email` (duplikat `profile_email`), `app_keywords`, `theme_color` (selalu kalah dari cookie template customizer) — tidak ada view/service yang mengonsumsinya. Ikut terhapus: array `$siteInfo` hardcode di `Landing\HomeController::index` (kode mati — selalu tertimpa View composer), read orphan `profile_video_url`, dan key `config('variables')` yang tak terpakai.
-   `instansi`/`wilayah` tables, models, controllers, services, jobs, console commands, and views (multi-tenancy is out of scope — see `S3_MVP_DESIGN.md` §1).
-   Boilerplate demo modules unrelated to S3 (Products CRUD, Sneat template demo pages, unauthenticated `/api/users` endpoint, `l5-swagger`/`maatwebsite/excel` packages).

### Security

-   **Deny-all `.htaccess` at the repo root**: the production app is deployed inside the cPanel document root (`~/soreang/laravel_soreang`) rather than a sibling folder, so without this, `.env`/`vendor/`/`app/`/`database/` would be directly reachable over HTTP. `public/.htaccess` overrides the deny for that folder only. Ships automatically with every FTP deploy.
-   Removed unauthenticated `/api/users` endpoint (`UserApiController`) that exposed user data with no auth guard.

## [1.4.0] - 2026-02-23

### Added

-   **Enhanced Code Generator**: `make:feature` now automatically generates full Blade views (Index, Create, Edit, Show) with Sneat-compatible templates.
-   **Namespace/Directory Support**: `make:feature` now supports subdirectories (e.g., `php artisan make:feature Admin/Post`), automatically handling folders and namespaces.

### Changed

-   **Profile Validation**: Refactored profile update logic to use a dedicated `ProfileRequest`.
-   **Development Guide**: Updated with instructions for the new generator capabilities.

### Fixed

-   **Profile Password**: Fixed issue where password was incorrectly required during profile info updates.

## [1.3.0] - 2026-02-05

### Added

-   **User Avatar**: Implemented support for uploading and displaying user avatars across profile and management views.
-   **System Health**: Added system health and maintenance status endpoints (`SystemController`).
-   **Enhanced RBAC**: Added new 'Visitor' role for restricted access.
-   **Audit Documentation**: Completed comprehensive technical audit for PHP 7.4 compatibility.

### Changed

-   **Framework Upgrade**: Upgraded to Laravel 12.x and PHP 8.2+ compatibility.
-   **Activity Log**: Refined log display and statistics.

## [1.2.0] - 2026-01-20

### Added

-   **Global Settings**: Comprehensive management system for website configuration (Logo, App Name, etc.) with dedicated UI.
-   **User Profile**: Dedicated page for users to manage their profile and password.
-   **Alerts**: Added new "warning" type to the Alert system.

### Changed

-   **Activity Logging**: Refactored logging logic for better maintainability and reliability.
-   **Authorization**: improved redirect logic for admin vs non-admin users and set default application timezone.
-   **UI/UX**: Redesigned Roles & Permissions interface.

### Fixed

-   **Sidebar**: Fixed active menu highlighting issues when navigating sub-menus.

## [1.1.0] - 2026-01-12

### Documentation

-   **Translation**: Translated all documentation (README, Guides) to English.
-   **Sponsorship**: Added sponsorship and license information.

### Removed

-   **Sponsor**: Removed Ko-fi link integration.

## [1.0.0] - 2026-01-04

### Added

-   **Base Template**: Finalized base Laravel template with RBAC (Role-Based Access Control).
-   **Product Management**: Module for managing products with CRUD operations.
-   **API Docs**: Integrated Swagger/OpenAPI for API documentation with Sanctum authentication.
-   **Seeders**: Multi-role seeders and default menu configurations.

### Fixed

-   **Auth**: Refined exception handler to correctly handle unauthenticated redirects for web routes.
-   **Validation**: Updated menu request validation logic.

### Documentation

-   Created step-by-step Development Guide.
-   Updated Creator information and project documentation.
