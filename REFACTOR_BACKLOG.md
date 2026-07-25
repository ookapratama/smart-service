# Refactor Backlog

Known tech debt in this base, recorded during the initial cleanup pass. HIGH-severity bugs, the `make:feature` generator, and the 2026-07-25 bugfix batch (validation/permission/leak fixes — see CHANGELOG) have been resolved; the items below are the remaining MEDIUM/LOW work.

## Medium

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
