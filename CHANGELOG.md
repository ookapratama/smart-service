# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

-   **Single-kecamatan schema redesign**: replaced the `instansi`/`wilayah` multi-tenant design with a single-kecamatan model targeting Kecamatan Soreang, Kota Parepare. New master table `kelurahan` (7 rows, seeded), new tables `agenda`, `galeri`, `notifikasi_wa`, `surat_counters` for upcoming MVP features. New role `warga` (passwordless, OTP-by-NIK login is a later phase) and `petugas` (renamed from `petugas-instansi`). New `DemoDataSeeder` (always seeded, idempotent) with sample tiket/pemohon spanning all status values. Full design doc: `S3_MVP_DESIGN.md`.
-   **Deploy webhook**: `POST /api/deploy/webhook` (`DeployWebhookController`) runs `migrate`/`db:seed`/cache/`queue:restart` on the production server, authenticated via a shared-secret token (`services.deploy.webhook_token`) instead of a login session.

### Changed

-   **`TiketService::generateNomorTiket()`**: nomor tiket generation moved to an atomic `TiketCounter::upsert()` + `lockForUpdate()` read-back, replacing a `first() ?: create()` pattern that race-lost when two requests hit the first request of a new periode simultaneously.
-   **`nik` and `nomor_tiket` are now globally unique** (previously scoped per-`instansi_id`).
-   **`tiket.pemohon_id` is `restrictOnDelete()`** (previously cascade) — deleting a pemohon no longer silently deletes their ticket history.
-   **CI/CD deploy pipeline**: switched from rsync-over-SSH to FTP (`SamKirkland/FTP-Deploy-Action`) for file upload, since GitHub Actions' runner IPs get their SSH connection reset on the target host. Post-deploy commands (`migrate`/`db:seed`/cache/`queue:restart`) moved from SSH (`appleboy/ssh-action`) to the new HTTP deploy webhook, triggered automatically at the end of every deploy. See `DEPLOYMENT.md` and `REFACTOR_BACKLOG.md` for details.

### Fixed

-   **`PengaduanPublicController::store()`** stored `detail_type` as an FQCN instead of the morph-map alias, breaking `whereHasMorph()` queries against publicly-submitted pengaduan. Now creates the tiket via `$pengaduan->tiket()->create([...])`, matching the morph-map alias convention used elsewhere.
-   **`LandingApiController::cekStatus()`**: `where('nomor_tiket', ...)->orWhereHas(...)` was unparenthesized, so any future added condition on the query would have silently leaked past the intended filter. Wrapped in a closure.
-   Typo "Sorean" → "Soreang" throughout landing pages, seeders, and controller copy; incorrect Kab. Bandung address/phone/postal-code placeholders corrected to Kota Parepare.

### Removed

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
