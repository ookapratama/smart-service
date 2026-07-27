# 🏢 Soreang Smart Service (S3)

Sistem pelayanan publik terintegrasi berbasis digital untuk tingkat kecamatan/kelurahan — persuratan online, pengaduan masyarakat, dan cek status permohonan, dirancang untuk bisa dipakai lebih dari satu instansi (multi-tenant).

Dibangun di atas fondasi admin panel Laravel dengan **Service-Repository Pattern**, **Audit Trail**, dan **RBAC granular**.

---

## 🌟 Fitur Utama

-   🏛️ **Manajemen Instansi Multi-Tenant** — hierarki kabupaten/kecamatan/kelurahan, dengan pencarian data wilayah resmi (sinkron dari [wilayah.id](https://wilayah.id)).
-   🎫 **Tiket sebagai Tulang Punggung** — setiap layanan (persuratan & pengaduan) menghasilkan nomor tiket yang bisa dipantau statusnya (baru → diproses → selesai/ditolak).
-   📄 **Smart Digital Service** — pelayanan persuratan online (Surat Keterangan, Surat Pengantar).
-   📢 **Smart Complaint** — pengaduan masyarakat per kategori.
-   👥 **Data Pemohon** — pencatatan warga pemohon layanan, NIK unik per instansi.
-   🏗️ **Service-Repository Pattern** — kodebase yang bersih, terstruktur, dan mudah diuji.
-   🛡️ **RBAC Granular** — role & permission per menu per aksi (Create, Read, Update, Delete), termasuk role khusus **Petugas Instansi** yang terikat ke satu instansi.
-   🕵️ **Activity Log (Audit Trail)** — mencatat setiap perubahan data secara otomatis (before/after snapshot).
-   ⚙️ **Global Settings & Branding** — kelola nama aplikasi, logo, favicon dari UI.
-   👤 **Profil & Impersonation** — kelola profil pengguna; Super Admin bisa login sebagai user lain untuk troubleshooting.
-   📁 **File Upload Manager** — penanganan file terpusat dengan auto-resize.
-   🎨 **Admin UI Premium** — Sneat Bootstrap 5, mode Dark/Light.
-   🤖 **Custom Code Generator** — scaffold modul CRUD lengkap dengan satu perintah.
-   📖 **Dokumentasi API** — Swagger (OpenAPI) siap pakai.

---

## 📁 Panduan Dokumentasi

| Panduan | Deskripsi |
| --- | --- |
| 📘 **[FEATURES_GUIDE.md](FEATURES_GUIDE.md)** | Overview lengkap fitur admin panel dasar. |
| 🛠 **[DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)** | Standar koding dan cara menambah modul baru. |
| 🕵️ **[ACTIVITY_LOG_GUIDE.md](ACTIVITY_LOG_GUIDE.md)** | Dokumentasi audit trail & monitoring pengguna. |
| 🔔 **[ALERT_SYSTEM_GUIDE.md](ALERT_SYSTEM_GUIDE.md)** | Cara pakai sistem SweetAlert & Toastr global. |
| 🚀 **[DEPLOYMENT.md](DEPLOYMENT.md)** | Deploy otomatis via GitHub Actions ke hosting cPanel. |
| 🧹 **[REFACTOR_BACKLOG.md](REFACTOR_BACKLOG.md)** | Utang teknis yang tercatat & yang sudah diperbaiki. |

---

## 🚀 Quick Start (Lokal)

### 1. Clone & Install

```bash
git clone https://github.com/ookapratama/smart-service.git
cd smart-service
composer install && npm install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database & Assets

```bash
php artisan migrate:fresh --seed
npm run build
```

### 4. Sinkronkan Data Wilayah (opsional, untuk pencarian Provinsi/Kabupaten/Kecamatan)

```bash
php artisan wilayah:sync
```

Perintah ini menarik data dari wilayah.id — bisa memakan waktu beberapa menit untuk sinkronisasi penuh (idempoten, aman diulang). Bisa juga dipicu lewat tombol **"Sinkronkan Data Wilayah"** di halaman Instansi setelah aplikasi jalan.

### 5. Jalankan Project

```bash
composer dev
```

---

## 🔑 Akun untuk Testing

Setelah `migrate:fresh --seed`, akun berikut tersedia (password: `password` untuk semua):

| Email | Role | Cakupan |
| --- | --- | --- |
| `superadmin@gmail.com` | Super Admin | Akses penuh platform |
| `admin@gmail.com` | Admin | Manajemen user/menu/activity-log + modul S3 |
| `petugas.soreang@gmail.com` | Petugas Instansi | Tiket + Pemohon, terikat Kecamatan Soreang |
| `petugas.pamekaran@gmail.com` | Petugas Instansi | Tiket + Pemohon, terikat Desa Pamekaran |
| `user@gmail.com` | User | Dashboard saja |

---

## 💡 Membuat Modul Baru

Butuh modul CRUD baru (mis. Product)? Pakai generator bawaan:

```bash
# Basic usage
php artisan make:feature Product

# Dengan subdirectory
php artisan make:feature Admin/User
```

Scaffolding mencakup Repository, Service, Controller, Request, dan **CRUD Blade views lengkap**. Lihat **[DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)** untuk detail.

---

## 📦 Tech Stack

-   **Backend**: Laravel 12.x, PHP 8.2+
-   **Frontend**: Bootstrap 5, Vite, jQuery (Sneat Template), Select2
-   **Database**: MySQL
-   **Data Wilayah**: Sinkron dari [wilayah.id](https://wilayah.id) API
-   **CI/CD**: GitHub Actions → auto-deploy ke hosting cPanel (lihat [DEPLOYMENT.md](DEPLOYMENT.md))
-   **Testing**: Pest PHP

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE).

_Dibangun di atas base admin panel oleh [Ooka Pratama](https://github.com/ookapratama)_
