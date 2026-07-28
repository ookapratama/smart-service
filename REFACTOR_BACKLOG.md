# Refactor Backlog

Known tech debt in this base, recorded during the initial cleanup pass. HIGH-severity bugs, the `make:feature` generator, and the 2026-07-25 bugfix batch (validation/permission/leak fixes — see CHANGELOG) have been resolved; the items below are the remaining MEDIUM/LOW work.

## Deferred from Fase C/D persuratan (2026-07-28)

- **WA send is synchronous in the request cycle.** `S3_MVP_DESIGN.md` §5.4 mandates a queued job dispatched `->afterCommit()` for every WA trigger (tiket creation + each status change); current code calls the notifier inline (blocking on gateway latency). Introduce a `KirimNotifikasiWaJob` + wire all call sites when the real HTTP gateway driver lands.
- **Phone normalization 08xx vs +628xx.** The same physical number in two formats is treated as two different numbers (OTP target resolution, cooldown keys, pemohon dedupe). Normalize to one canonical form (e.g. `628…`) at the service boundary.
- **Preview draft surat for warga (§5.3).** "Bisa lihat preview draft surat" after submit = on-demand render, never stored. Deferred to the warga-login phase; `SuratService::generatePdf()` template resolution is already reusable for it.
- **OTP cooldown response returns the constant, not exact seconds.** On `reason: cooldown`, `retry_after` is always the full `RESEND_COOLDOWN` (60) instead of the actual remaining seconds of the cooldown cache entry. Store the expiry timestamp in the cooldown cache value and compute the remainder.
- **Form re-verify UX after failed submit.** When submit fails validation after OTP passed, the session flag survives but the page reloads with the OTP UI in its initial state — the user can't tell whether they must re-verify. Surface the still-verified state (or remaining TTL) in the form on re-render.

## Medium

- **`DeployWebhookController::run` executes migrate/seed/cache/queue-restart synchronously inside one HTTP request.** Shared-hosting PHP has a max execution time (often 30-60s). Fine at the current schema/data size (~11s locally including all six commands), but if the migration/seed set grows substantially this could start timing out mid-command, leaving `config:cache`/`route:cache` unrun after a successful `migrate`. If it ever becomes a problem, consider queuing the commands instead and having the webhook just dispatch + return 202, with a second webhook call (or the queue-worker cron already documented in `DEPLOYMENT.md` §6) to check completion.
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

- **`wilayah:sync` is fully sequential — one full national sync takes 10-90+ minutes.** ~514 kecamatan requests are made one at a time (with a 100ms courtesy delay), and wall-clock time is dominated by wilayah.id's own response latency, which varied a lot in practice. A future improvement: batch the district-level fetches with Laravel's `Http::pool()` (e.g. 10-20 concurrent requests) to cut this to a few minutes. Not urgent — it's a one-time/rare command, and the sync is idempotent and safe to rerun if interrupted.
- **Repeated avatar-URL expression.** `$x->avatar ? asset('storage/'.$x->avatar) : asset('assets/img/avatars/1.png')` appears in 6+ places. Add a `User::getAvatarUrlAttribute()` accessor or an `<x-avatar>` component (note `Media::getUrlAttribute()` already exists).
- **`Products` model is plural.** Laravel convention is singular `Product`; the plural propagates through `ProductsService/Repository/Request`. Cosmetic, but the base sets the example new code copies.
- **Commented-out stubs.** `prepareForValidation` body in `BaseRequest`; `defaultLanguage` config in `ViewConfigHelper`.
- **Generator stub still assumes a single `name` column.** `make:feature` now marks this with TODO comments, but full DB-column introspection (generating views/migration from actual columns) is not implemented. Optional future enhancement.
- **CLI emoji in `make:feature` output** (✅💡📌) can mojibake on legacy Windows `cmd`. Swap for plain `[OK]`/`[INFO]` tags if that matters.

## Fixed (2026-07-26 — deploy pipeline: SSH blocked on host, switched to FTP + webhook)

- ✅ **GitHub Actions couldn't reach the cPanel host over SSH at all** — first `Connection timed out`, then (after confirming the port) `kex_exchange_identification: read: Connection reset by peer`. Both signatures point to provider-side IP filtering on the SSH port (Domainesia/CloudLinux shared hosting), not a workflow misconfiguration — the reset-during-handshake specifically rules out "wrong port" (that gives `Connection refused`, not a mid-handshake reset). `.github/workflows/deploy.yml` now uploads via `SamKirkland/FTP-Deploy-Action@v4.4.0` (port 21, unaffected) instead of `easingthemes/ssh-deploy`. Post-deploy commands (`migrate`/`db:seed`/cache/`queue:restart`) — previously run via `appleboy/ssh-action`, now impossible over FTP — moved to a new authenticated HTTP route, `POST /api/deploy/webhook` (`app/Http/Controllers/Api/DeployWebhookController.php`), gated by a shared-secret token (`X-Deploy-Token` header vs. `config('services.deploy.webhook_token')`, compared with `hash_equals()`) since there's no login session available from CI. `storage:link` dropped from the automated command list — it's idempotent-unfriendly enough (and one-time in nature) that it's now a manual step in `DEPLOYMENT.md` §2/§4 instead.
- ✅ **`EXCLUDE`/`exclude` input format differs between the two actions** — `easingthemes/ssh-deploy` wanted a single comma-separated string; `SamKirkland/FTP-Deploy-Action` wants a YAML multi-line block of glob patterns (`**/.git*`, `**/node_modules/**`, etc.), confirmed against the action's own README before writing it, after getting bitten by guessing the wrong format for the SSH action earlier in the same investigation.

## Fixed (2026-07-26 — CI `ViteManifestNotFoundException`)

- ✅ **Every full-page-render Feature test 500'd in GitHub Actions** (`PermissionPestTest`, `S3CrudSmokeTest`, `WilayahSyncButtonTest` — 7 tests total), passing only locally. Root cause: `deploy.yml`'s `test` job never runs `npm run build`, so `public/build/manifest.json` doesn't exist there (it's gitignored) — passed locally only because a stale manifest from a previous local build happened to be on disk. Fixed in `tests/Pest.php` by chaining `->beforeEach(fn () => $this->withoutVite())` onto the existing `uses(...)->in('Feature')` call, applying to every Feature test globally. Note: a separate top-level `beforeEach(...)->in('Feature')` call did **not** reliably apply — must be chained onto the same `uses()` builder. Verified by physically removing `public/build/` locally and confirming the full suite (35/35) still passes.

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

## Added (2026-07-25 — wilayah.id sync)

- ✅ **New `wilayah` reference table** (provinsi/kabupaten-kota/kecamatan, ~7,837 rows), synced from https://wilayah.id/api/ via `WilayahSyncService` + `php artisan wilayah:sync`. Deliberately separate from `instansi` — see `CLAUDE.md`.
- ✅ Cascading dropdown endpoint `GET /wilayah/{code}/children`, wired into the Instansi create/edit forms to autofill Name/Kode from official data.
- ✅ 5 new Pest tests (`tests/Feature/WilayahSyncTest.php`), all using `Http::fake()` — no live network calls in the suite.
- ✅ **"Sinkronkan Data Wilayah" button** on `instansi/index.blade.php`, dispatching `App\Jobs\SyncWilayahJob` to the queue (not synchronous — the sync can take well over an hour). Status (`idle`/`running`/`failed`) + last-synced timestamp + failed-request count tracked via cache, shared between the button and the `wilayah:sync` CLI command so status is accurate regardless of trigger. 4 new Pest tests (`tests/Feature/WilayahSyncButtonTest.php`) using `Queue::fake()`.
