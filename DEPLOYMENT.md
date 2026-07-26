# Deployment Guide (GitHub Actions → cPanel)

Target environment (confirmed): cPanel account `simbbgps`, subdomain `soreang.simbbgtksulsel.com`, document root `/home/simbbgps/soreang` (already exists — cPanel put `.htaccess`, `php.ini`, `.user.ini`, and `.well-known/` there for the domain's SSL/PHP config; **never delete these**).

`.github/workflows/deploy.yml` runs on every push to `main`: it runs the Pest test suite first (deploy is blocked if tests fail), then builds the app (Composer + npm) on the GitHub Actions runner and rsyncs the built result to the server over SSH, then runs a short list of `artisan` commands on the server itself.

Composer and `npm run build` run **on the CI runner, not on the shared host** — shared-hosting memory/time limits make `composer install` there unreliable. The server only ever runs lightweight commands: `migrate`, `db:seed`, `config:cache`, `route:cache`, `view:cache`, `storage:link`, `queue:restart`.

`db:seed` runs on **every** deploy (not just the first). This is safe because every seeder in this app uses `updateOrCreate`/`updateOrInsert` — re-running them never duplicates rows, it just re-asserts the same baseline data (roles, menus, demo instansi, demo accounts, master data). Real data added through the app (tickets, pemohon, uploaded files) is untouched — seeders never write to those tables.

## 1. One-time SSH key setup

1. Generate a dedicated deploy keypair (don't reuse your personal key):
    ```bash
    ssh-keygen -t ed25519 -C "github-actions-deploy" -f deploy_key -N ""
    ```
2. In cPanel → **SSH Access** → **Manage SSH Keys** → **Import Key**, paste the contents of `deploy_key.pub`, then **Authorize** it.
3. Keep `deploy_key` (the private key) — it goes into a GitHub Secret in step 3, never committed to the repo.

## 2. Wiring the app to the document root (one-time, manual)

The document root `~/soreang` is a **real, already-populated directory** (SSL validation + PHP config files live there) — it is not empty and should not be deleted or replaced wholesale. The app itself deploys to a **separate sibling folder**, `~/laravel_soreang`, and we symlink Laravel's `public/` assets into the document root individually. This is the same technique proven by the existing `www -> public_html` symlink already on this account.

Run this once, by hand, over SSH, **after the first CI deploy has synced code into `~/laravel_soreang`** (steps in section 4):

```bash
cd ~/soreang

# Back up cPanel's placeholder .htaccess before replacing it with Laravel's own
# (Laravel's rewrite rules are required for routing — pretty URLs, hiding index.php).
mv .htaccess .htaccess.cpanel-backup

# Symlink Laravel's public/ entry points and assets into the document root.
ln -s ~/laravel_soreang/public/index.php index.php
ln -s ~/laravel_soreang/public/.htaccess .htaccess
ln -s ~/laravel_soreang/public/build build

# Only if these exist in public/ — check first with: ls ~/laravel_soreang/public
ln -s ~/laravel_soreang/public/robots.txt robots.txt
ln -s ~/laravel_soreang/public/favicon.ico favicon.ico

# For file uploads served via `php artisan storage:link` (creates
# laravel_soreang/public/storage -> laravel_soreang/storage/app/public):
ln -s ~/laravel_soreang/public/storage storage
```

Do **not** touch `php.ini`, `.user.ini`, or `.well-known/` — leave them exactly as cPanel created them.

This is a **one-time** setup. Future deploys only update the contents of `~/laravel_soreang` (via rsync — see section 4); the symlinks in `~/soreang` keep pointing at the same (now-updated) files automatically, so nothing here needs to be redone per deploy.

## 3. GitHub Secrets to add

Repo (`ookapratama/smart-service`) → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**:

| Secret           | Value                                                                       |
| ---------------- | --------------------------------------------------------------------------- |
| `DEPLOY_HOST`    | Server hostname/IP for SSH (cPanel → General Information)                   |
| `DEPLOY_USER`    | `simbbgps`                                                                  |
| `DEPLOY_SSH_KEY` | Contents of the **private** key (`deploy_key`) from step 1                  |
| `DEPLOY_PORT`    | SSH port (check cPanel → SSH Access — shared hosts often use a non-22 port) |
| `DEPLOY_PATH`    | `/home/simbbgps/laravel_soreang` (the **app root**, not `~/soreang`)        |

## 4. One-time server-side setup

Before the first deploy:

1. Create the target directory: `mkdir -p ~/laravel_soreang`
2. Push to `main` (or trigger the workflow manually — see section 6) so the first rsync populates `~/laravel_soreang` with code.
3. Create the production `.env` **directly on the server**, in `~/laravel_soreang/.env` (copy from `.env.example`, fill in real DB credentials, `APP_KEY`, `APP_URL=https://soreang.simbbgtksulsel.com`, `APP_ENV=production`, `APP_DEBUG=false`). **Never commit `.env`** — `deploy.yml` excludes it from rsync, so it's untouched on every future deploy.
4. Create the MySQL database + user via cPanel → **MySQL Databases**, put those credentials in `.env`.
5. Generate the app key: `cd ~/laravel_soreang && php artisan key:generate`.
6. Do the one-time symlink setup from section 2.
7. Run `php artisan migrate --force && php artisan db:seed --force` once by hand the first time (subsequent deploys run both automatically via the workflow).
8. After this, the app has demo login accounts ready — see **"Test accounts"** below.

### Test accounts

Seeded automatically on every deploy (`UserSeeder`), password `password` for all:

| Email | Role | Scope |
|---|---|---|
| `superadmin@gmail.com` | Super Admin | Full platform access (bypasses all permission checks) |
| `admin@gmail.com` | Admin | Platform-level admin (user/menu/activity-log management + S3 modules) |
| `petugas.soreang@gmail.com` | Petugas Instansi | Tiket + Pemohon only, tied to Kecamatan Soreang |
| `petugas.pamekaran@gmail.com` | Petugas Instansi | Tiket + Pemohon only, tied to Desa Pamekaran |
| `user@gmail.com` | User | Dashboard only |

**Change or remove these before real citizen data goes live** — they're seeded for testing/demo purposes with a known weak password. Once this stops being a staging/demo deployment, either delete `UserSeeder`'s calls from `DatabaseSeeder` or change their passwords directly in the database.

## 5. Known gotcha: asdf-managed PHP/Composer PATH

This account uses **asdf** (`.tool-versions`/`.asdf` in the home directory) to manage PHP/Composer versions. A normal interactive `ssh` login sources `.bash_profile`, which loads asdf's shims into `PATH` — but the GitHub Actions SSH step runs a single non-interactive command, which does **not** source it automatically. `deploy.yml` already adds `source ~/.bash_profile || true` before running any `php`/`artisan` command to compensate. If a deploy ever fails with `php: command not found`, this is the first thing to check — confirm `cat ~/.bash_profile` actually initializes asdf (it should include a line sourcing `~/.asdf/asdf.sh` or similar).

## 6. Important: queue jobs on shared hosting

This app uses a queued job (`SyncWilayahJob`, triggered by the "Sinkronkan Data Wilayah" button) that needs something to actually process the `jobs` table — locally that's `php artisan queue:listen` via `composer dev`. **Shared hosting cannot run a persistent background process** like `queue:work`, so jobs would sit unprocessed forever without one of these:

- **Recommended:** add a cPanel **Cron Job** (cPanel → Cron Jobs) running every minute:
    ```
    * * * * * source /home/simbbgps/.bash_profile && cd /home/simbbgps/laravel_soreang && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
    ```
    This starts a worker, drains whatever's queued, and exits — safe to run every minute since `--stop-when-empty` prevents overlap buildup. Note the same asdf `source` gotcha from section 5 applies here too.
- **Simpler alternative:** set `QUEUE_CONNECTION=sync` in production `.env`. Jobs then run immediately inline when dispatched — no cron needed, but the wilayah sync button will block the HTTP request for its full duration again (defeating the reason it was made a queued job in the first place). Only use this if a cron job genuinely isn't available.

## 7. Manually triggering a deploy

The workflow also supports `workflow_dispatch` — trigger it by hand from the repo's **Actions** tab → **Deploy to Production** → **Run workflow**, without needing a new commit.
