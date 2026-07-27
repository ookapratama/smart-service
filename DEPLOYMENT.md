# Deployment Guide (GitHub Actions → cPanel)

Target environment (confirmed): cPanel account `simbbgps`, subdomain `soreang.simbbgtksulsel.com`, document root `/home/simbbgps/soreang` (already exists — cPanel put `.htaccess`, `php.ini`, `.user.ini`, and `.well-known/` there for the domain's SSL/PHP config; **never delete these**). The app deploys into `~/soreang/laravel_soreang` — a folder **nested inside** the document root, not a sibling of it. Because that puts the whole app (`.env`, `vendor/`, `app/`, `database/`, ...) inside the web-servable tree, a repo-root `.htaccess` (deployed automatically with every FTP upload) blocks direct HTTP access to everything except `public/` — see section 2.

`.github/workflows/deploy.yml` runs on every push to `main`: it runs the Pest test suite first (deploy is blocked if tests fail), then builds the app (Composer + npm) on the GitHub Actions runner and uploads the built result to the server over **FTP**, then triggers a short list of `artisan` commands via an **HTTP webhook** on the app itself.

**Why FTP + a webhook instead of rsync-over-SSH:** GitHub Actions' runner IPs get their SSH connection reset mid-handshake on this host (`kex_exchange_identification: read: Connection reset by peer`) — almost certainly provider-side IP filtering on the SSH port (Domainesia/CloudLinux shared hosting), not a config mistake. FTP (port 21) is unaffected. Since FTP can only move files, not run commands, a protected route — `POST /api/deploy/webhook`, `app/Http/Controllers/Api/DeployWebhookController.php` — runs `migrate`/`db:seed`/the cache commands/`queue:restart` instead, authenticated via a shared-secret token (`X-Deploy-Token` header, compared with `hash_equals()`) rather than a login session, since CI has no browser/cookies.

Composer and `npm run build` run **on the CI runner, not on the shared host** — shared-hosting memory/time limits make `composer install` there unreliable. The server only ever runs lightweight commands via the webhook: `migrate`, `db:seed`, `config:cache`, `route:cache`, `view:cache`, `queue:restart`. (`storage:link` is no longer run on every deploy — see section 4, it's a one-time step now.)

`db:seed` runs on **every** deploy (not just the first). This is safe because every seeder in this app uses `updateOrCreate`/`updateOrInsert` — re-running them never duplicates rows, it just re-asserts the same baseline data (roles, menus, demo instansi, demo accounts, master data). Real data added through the app (tickets, pemohon, uploaded files) is untouched — seeders never write to those tables.

## 1. One-time FTP credentials

1. In cPanel → **FTP Accounts**, either reuse the main account's FTP login or (recommended) create a dedicated FTP account scoped to `laravel_soreang/` only, so a leaked GitHub Secret can't reach the rest of the hosting account.
2. Note the **server**, **username**, **password**, and **port** (usually `21` unless cPanel/your host says otherwise) — these go into GitHub Secrets in step 3.
3. Confirm the port actually works before wiring up CI: SSH on this host resets connections from unfamiliar IPs, so it's worth ruling out the same for FTP too — test with any FTP client (FileZilla, WinSCP) from your own machine first.

## 2. Wiring the app to the document root (one-time, manual)

The document root `~/soreang` is a **real, already-populated directory** (SSL validation + PHP config files live there) — it is not empty and should not be deleted or replaced wholesale. The app deploys into `~/soreang/laravel_soreang` (nested inside the docroot, per how the FTP account/path was set up), and we symlink Laravel's `public/` assets out to the document root's root individually — the URL still needs to resolve to `https://soreang.simbbgtksulsel.com/`, not `.../laravel_soreang/public/`. This is the same technique proven by the existing `www -> public_html` symlink already on this account.

**Security note:** because the app folder sits inside the web-servable docroot instead of outside it, `.env`/`vendor/`/`app/`/`database/` etc. would otherwise be directly requestable over HTTP (e.g. `https://soreang.simbbgtksulsel.com/laravel_soreang/.env`). Two `.htaccess` files close this: a deny-all one at the **repo root** (ships automatically with every FTP deploy, so it can't be forgotten) and an override inside `public/.htaccess` that re-allows just that folder. Both are already committed — nothing to do here except verify (step below).

Run this once, by hand, via **cPanel → Advanced → Terminal** (the browser-based shell — this works even though external SSH from GitHub Actions is blocked, since it doesn't go through the same filtered port), **after the first CI deploy has synced code into `~/soreang/laravel_soreang`** (steps in section 4):

```bash
cd ~/soreang

# Back up cPanel's placeholder .htaccess before replacing it with Laravel's own
# (Laravel's rewrite rules are required for routing — pretty URLs, hiding index.php).
mv .htaccess .htaccess.cpanel-backup

# Symlink Laravel's public/ entry points and assets into the document root.
ln -s ~/soreang/laravel_soreang/public/index.php index.php
ln -s ~/soreang/laravel_soreang/public/.htaccess .htaccess
ln -s ~/soreang/laravel_soreang/public/build build

# Only if these exist in public/ — check first with: ls ~/soreang/laravel_soreang/public
ln -s ~/soreang/laravel_soreang/public/robots.txt robots.txt
ln -s ~/soreang/laravel_soreang/public/favicon.ico favicon.ico

# For file uploads served via `php artisan storage:link` (creates
# laravel_soreang/public/storage -> laravel_soreang/storage/app/public):
ln -s ~/soreang/laravel_soreang/public/storage storage
```

Do **not** touch `php.ini`, `.user.ini`, or `.well-known/` — leave them exactly as cPanel created them.

This is a **one-time** setup. Future deploys only update the contents of `~/soreang/laravel_soreang` (via FTP — see section 4); the symlinks in `~/soreang` keep pointing at the same (now-updated) files automatically, so nothing here needs to be redone per deploy.

**Verify the `.htaccess` protection** once the symlinks and a real `.env` are in place:
```bash
curl -I https://soreang.simbbgtksulsel.com/laravel_soreang/composer.json   # expect 403
curl -I https://soreang.simbbgtksulsel.com/                                # expect 200
```
If the first one returns anything but `403` (e.g. `200`, or the raw file contents), the deny-all `.htaccess` didn't take effect — double check it actually uploaded to `~/soreang/laravel_soreang/.htaccess` (it's a dotfile — some FTP clients/servers hide or mishandle dotfiles by default) and that `AllowOverride` is enabled for the account (it is by default on cPanel, but worth confirming with hosting support if this check fails).

## 3. GitHub Secrets to add

Repo (`ookapratama/smart-service`) → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**:

| Secret                 | Value                                                                                                                                                                                  |
| ---------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `DEPLOY_HOST`          | FTP server hostname (from cPanel → FTP Accounts, e.g. `asti.id.domainesia.com`)                                                                                                        |
| `DEPLOY_USER`          | FTP username from step 1                                                                                                                                                               |
| `DEPLOY_FTP_PASSWORD`  | FTP password from step 1                                                                                                                                                               |
| `DEPLOY_PORT`          | FTP port, usually `21`                                                                                                                                                                 |
| `DEPLOY_PATH`          | `soreang/laravel_soreang/` — **must end with a trailing slash** (FTP-Deploy-Action requirement); path is relative to the FTP account's root (confirm with `ls`/an FTP client — if the account is chrooted straight into `~/soreang`, drop the `soreang/` prefix) |
| `DEPLOY_WEBHOOK_URL`   | `https://soreang.simbbgtksulsel.com/api/deploy/webhook`                                                                                                                                |
| `DEPLOY_WEBHOOK_TOKEN` | A random secret — generate with `php artisan tinker --execute="echo Str::random(40);"`, must match the server's `.env` `DEPLOY_WEBHOOK_TOKEN` exactly                                  |

## 4. One-time server-side setup

Before the first deploy:

1. Create the target directory (via cPanel Terminal or File Manager): `mkdir -p ~/soreang/laravel_soreang`
2. Push to `main` (or trigger the workflow manually — see section 7) so the first FTP upload populates `~/soreang/laravel_soreang` with code. This run's webhook step will fail (expected — `.env` doesn't exist yet, so the app can't boot to respond) once files are up.
3. Create the production `.env` **directly on the server** (cPanel Terminal or File Manager), in `~/soreang/laravel_soreang/.env` — copy from `.env.example`, fill in real DB credentials, `APP_KEY`, `APP_URL=https://soreang.simbbgtksulsel.com`, `APP_ENV=production`, `APP_DEBUG=false`, and the same `DEPLOY_WEBHOOK_TOKEN` value you put in the GitHub Secret. **Never commit `.env`** — `deploy.yml`'s `exclude` list keeps it out of the FTP upload, so it's untouched on every future deploy.
4. Create the MySQL database + user via cPanel → **MySQL Databases**, put those credentials in `.env`.
5. Generate the app key: `cd ~/soreang/laravel_soreang && php artisan key:generate`.
6. Do the one-time symlink setup from section 2, including `php artisan storage:link` (no longer run automatically on every deploy — see the note above the sections).
7. Run `php artisan migrate --force && php artisan db:seed --force` once by hand the first time (subsequent deploys run both automatically via the webhook).
8. After this, the app has demo login accounts ready — see **"Test accounts"** below. From the next push onward, the full pipeline (FTP upload → webhook → migrate/seed/cache/queue-restart) runs unattended.

### Test accounts

Seeded automatically on every deploy (`UserSeeder`), password `password` for all:

| Email                         | Role             | Scope                                                                 |
| ----------------------------- | ---------------- | --------------------------------------------------------------------- |
| `superadmin@gmail.com`        | Super Admin      | Full platform access (bypasses all permission checks)                 |
| `admin@gmail.com`             | Admin            | Platform-level admin (user/menu/activity-log management + S3 modules) |
| `petugas.soreang@gmail.com`   | Petugas Instansi | Tiket + Pemohon only, tied to Kecamatan Soreang                       |
| `petugas.pamekaran@gmail.com` | Petugas Instansi | Tiket + Pemohon only, tied to Desa Pamekaran                          |
| `user@gmail.com`              | User             | Dashboard only                                                        |

**Change or remove these before real citizen data goes live** — they're seeded for testing/demo purposes with a known weak password. Once this stops being a staging/demo deployment, either delete `UserSeeder`'s calls from `DatabaseSeeder` or change their passwords directly in the database.

## 5. Known gotcha: asdf-managed PHP/Composer PATH

This account uses **asdf** (`.tool-versions`/`.asdf` in the home directory) to manage PHP/Composer versions. A normal interactive login (including cPanel's Terminal) sources `.bash_profile`, which loads asdf's shims into `PATH` — this matters for the **manual one-time steps** in sections 2 and 4 (`php artisan key:generate`, `storage:link`, the first `migrate`/`db:seed`) and for the **queue cron job** in section 6, both of which run `php`/`artisan` directly. If `php: command not found` shows up in either context, confirm `cat ~/.bash_profile` actually initializes asdf (should source `~/.asdf/asdf.sh` or similar), or explicitly `source ~/.bash_profile` first. This does **not** affect `deploy.yml` itself anymore — the workflow no longer runs any command on the server directly; the webhook route runs inside the already-booted app, which doesn't need shell PATH resolution at all.

## 6. Important: queue jobs on shared hosting

This app uses a queued job (`SyncWilayahJob`, triggered by the "Sinkronkan Data Wilayah" button) that needs something to actually process the `jobs` table — locally that's `php artisan queue:listen` via `composer dev`. **Shared hosting cannot run a persistent background process** like `queue:work`, so jobs would sit unprocessed forever without one of these:

-   **Recommended:** add a cPanel **Cron Job** (cPanel → Cron Jobs) running every minute:
    ```
    * * * * * source /home/simbbgps/.bash_profile && cd /home/simbbgps/soreang/laravel_soreang && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
    ```
    This starts a worker, drains whatever's queued, and exits — safe to run every minute since `--stop-when-empty` prevents overlap buildup. Note the same asdf `source` gotcha from section 5 applies here too.
-   **Simpler alternative:** set `QUEUE_CONNECTION=sync` in production `.env`. Jobs then run immediately inline when dispatched — no cron needed, but the wilayah sync button will block the HTTP request for its full duration again (defeating the reason it was made a queued job in the first place). Only use this if a cron job genuinely isn't available.

## 7. Manually triggering a deploy

The workflow also supports `workflow_dispatch` — trigger it by hand from the repo's **Actions** tab → **Deploy to Production** → **Run workflow**, without needing a new commit.
