# Refactor Backlog

Known tech debt in this base, recorded during the initial cleanup pass. HIGH-severity bugs, the `make:feature` generator, and the 2026-07-25 bugfix batch (validation/permission/leak fixes — see CHANGELOG) have been resolved; the items below are the remaining MEDIUM/LOW work.

## Medium

- **No tenant resolution yet, so `instansi_id` is picked manually.** `BelongsToInstansi` only scopes/auto-fills when `app()->bound('currentInstansi')`; until the subdomain-resolution middleware exists (Phase 1 follow-up), admin forms (Pemohon create/edit) require picking the instansi manually and `Tiket`/`Pemohon` index pages show all tenants at once. Expected at this stage, but revisit once middleware lands.
- **`SettingService` cache key is global, not per-tenant.** `$cacheKey = 'app_settings'` (`app/Services/SettingService.php`) will collide once tenant-specific settings rows are actually written (schema already supports `instansi_id`, all current rows are global/NULL). Needs to become tenant-aware, e.g. `app_settings:{instansiId|global}`, before Phase 3 (tenant landing/theming) work begins.
- **Nomor-tiket counter upsert is MySQL-only.** `tiket_counters` (race-safe sequence backing) is designed around `INSERT ... ON DUPLICATE KEY UPDATE ... LAST_INSERT_ID(...)`, which has no SQLite equivalent. The service that uses it (Phase 2+) needs a driver-aware path so `RefreshDatabase` tests on SQLite still pass.
- **`LogsActivity` + `status_log` both write on every Tiket status transition.** Acceptable for MVP; revisit with a `$logAttributes` whitelist on `Tiket` if `activity_logs` volume becomes a problem, since `status_log` already carries the same domain history.
- **`impersonate.start/{id}` has no permission middleware.** Any authenticated user can hit the route; authorization (if any) lives inside the controller only. Verify and put it behind an explicit permission or role check. _(Found during 2026-07-25 investigation.)_
- **Inline validation instead of FormRequests.** `AuthController::login/register`, `ProfileController::updatePassword`, and `ProductsController::importExcel` validate inline. Route them through FormRequests for consistency.
- **No `PermissionService`.** `PermissionController::update` has ~50 lines of permission-mapping + sync + activity-logging duplicated across its two branches. Extract to a `PermissionService` (every other domain has one).
- **Layer violation in `ProductsController::destroy`.** It queries `\App\Models\Media::where('path', ...)` directly from the controller, bypassing the service/repository layers. Move media cleanup behind a service.
- **Hardcoded branding in `AppServiceProvider::boot()`.** `templateAuthor`, `templateDomain = 'localhost'`, the Pixinvent `documentation` URL, and `templateVersion = '1.0.0'` are hardcoded. Move to `config/variables.php` / env.

## Low

- **Repeated avatar-URL expression.** `$x->avatar ? asset('storage/'.$x->avatar) : asset('assets/img/avatars/1.png')` appears in 6+ places. Add a `User::getAvatarUrlAttribute()` accessor or an `<x-avatar>` component (note `Media::getUrlAttribute()` already exists).
- **`Products` model is plural.** Laravel convention is singular `Product`; the plural propagates through `ProductsService/Repository/Request`. Cosmetic, but the base sets the example new code copies.
- **Commented-out stubs.** `prepareForValidation` body in `BaseRequest`; `defaultLanguage` config in `ViewConfigHelper`.
- **Generator stub still assumes a single `name` column.** `make:feature` now marks this with TODO comments, but full DB-column introspection (generating views/migration from actual columns) is not implemented. Optional future enhancement.
- **CLI emoji in `make:feature` output** (✅💡📌) can mojibake on legacy Windows `cmd`. Swap for plain `[OK]`/`[INFO]` tags if that matters.
- **`PermissionPestTest > admin can view permission index` fails without built assets.** The test renders a full Blade page with `@vite`; in a checkout without `npm run build` it throws `ViteManifestNotFoundException`. Add `$this->withoutVite()` to that test (or build assets in CI).

## Fixed (2026-07-25 batch)

- ✅ Web forms got raw JSON 422 — `failedValidation` branching moved into `BaseRequest`, duplicates deleted.
- ✅ `SettingController::update` upload/input validation — new `SettingRequest`.
- ✅ `check.permission` fail-open default + dead camelCase actionMap keys — full-route-name map, fails closed (403).
- ✅ `importExcel` leaked raw exception messages — now `report($e)` + generic message.
- ✅ Dead `$settings` assignment in `SettingController::index`.
- ✅ `console.log` + unconditional "Deleted!" alert in `laravel-user-management.js` delete flow.
- ✅ Avatar validation rules aligned (`UserRequest` now matches `ProfileRequest`).
- ✅ **`isMenuItemActive`/`hasActiveChild` redeclaration crash.** `resources/views/layouts/sections/menu/verticalMenu.blade.php` declared two plain PHP functions with no `function_exists()` guard — harmless in real request-per-process PHP-FPM traffic, but fatal (`Cannot redeclare function`) in any Feature test that renders two full pages in the same test method, and would also break under Octane/Swoole. Wrapped both in `function_exists()` guards.

## Fixed (2026-07-25 CRUD batch — Instansi/JenisSurat/KategoriPengaduan/Pemohon/Tiket)

- ✅ Full admin CRUD scaffolded via `make:feature` + manual customization for `Instansi` (hierarchy/level/kode), `JenisSurat`, `KategoriPengaduan`, `Pemohon` (NIK unique per tenant).
- ✅ `Tiket` deliberately given **monitoring + status-transition only** (index/show/update-status), not generic create/edit — `detail_type`/`detail_id` are non-nullable morphs, so a generic form would crash on save; real ticket creation belongs to the Phase 3 submission flow. `TiketService::updateStatus()` validates transitions via `TiketStatus::transitions()` and writes a `status_log` row.
- ✅ Sidebar menu + RBAC seeded via new `S3MenuSeeder` (Instansi top-level; Master Data > Jenis Surat, Kategori Pengaduan; Pelayanan > Tiket, Data Pemohon), wired into `DatabaseSeeder`.
- ✅ New regression tests: `tests/Feature/S3CrudSmokeTest.php` (6 tests covering all 5 modules end-to-end, including the status-transition guard).
