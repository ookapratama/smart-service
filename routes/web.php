<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AgendaController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\JadwalPelayananController;
use App\Http\Controllers\Admin\JenisSuratController;
use App\Http\Controllers\Admin\KategoriPengaduanController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PemohonController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\TiketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Landing\AgendaPublicController;
use App\Http\Controllers\Landing\BeritaPublicController;
use App\Http\Controllers\Landing\CekStatusPublicController;
use App\Http\Controllers\Landing\GaleriPublicController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\OtpController;
use App\Http\Controllers\Landing\PengaduanPublicController;
use App\Http\Controllers\Landing\PengajuanSuratPublicController;
use Illuminate\Support\Facades\Route;

// Public Landing Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public Berita Routes (Landing Page Template)
Route::get('/berita', [BeritaPublicController::class, 'index'])->name('berita.public.index');
Route::get('/berita/{slug}', [BeritaPublicController::class, 'show'])->name('berita.public.show');

// Public Galeri Routes
Route::get('/galeri', [GaleriPublicController::class, 'index'])->name('galeri.public.index');
Route::get('/galeri/{slug}', [GaleriPublicController::class, 'show'])->name('galeri.public.show');

// Public Agenda Routes
Route::get('/agenda', [AgendaPublicController::class, 'index'])->name('agenda.public.index');
Route::get('/agenda/{slug}', [AgendaPublicController::class, 'show'])->name('agenda.public.show');

// Public Cek Status Route
Route::get('/cek-status', [CekStatusPublicController::class, 'index'])->name('cek-status.index');

// Public Pengaduan Routes
Route::get('/pengaduan', [PengaduanPublicController::class, 'index'])->name('pengaduan.index');
Route::get('/pengaduan/create', [PengaduanPublicController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan/cek-nik', [PengaduanPublicController::class, 'cekNik'])->name('pengaduan.cek-nik');
Route::post('/pengaduan', [PengaduanPublicController::class, 'store'])->name('pengaduan.store');
Route::get('/pengaduan/sukses/{nomor_tiket}', [PengaduanPublicController::class, 'sukses'])->name('pengaduan.sukses');

// Public Persuratan Routes
Route::get('/surat', [PengajuanSuratPublicController::class, 'index'])->name('surat.index');
Route::post('/surat', [PengajuanSuratPublicController::class, 'store'])->name('surat.store');
Route::get('/surat/sukses/{nomor_tiket}', [PengajuanSuratPublicController::class, 'sukses'])->name('surat.sukses');
// Unduh PDF surat jadi: gerbang ganda nomor tiket + NIK (§3 — NIK bukan otentikasi tunggal).
// Throttle per IP: tanpa ini NIK bisa di-brute-force terhadap nomor tiket yang diketahui.
Route::get('/surat/unduh/{nomor_tiket}', [PengajuanSuratPublicController::class, 'unduh'])->name('surat.unduh')->middleware('throttle:10,1');
Route::get('/surat/{jenisSurat}', [PengajuanSuratPublicController::class, 'create'])->name('surat.create');

// Halaman panduan penggunaan layanan (bahasa awam)
Route::get('/panduan', [HomeController::class, 'panduan'])->name('panduan');

// OTP endpoints (AJAX dari halaman landing — session + CSRF)
Route::post('/otp/request', [OtpController::class, 'request'])->name('otp.request');
Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Dashboard (admin landing after login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User CRUD routes
    Route::resource('user', UserController::class)->middleware('check.permission:user.index');

    // Role & Menu Management
    Route::resource('role', RoleController::class)->middleware('check.permission:role.index');
    Route::resource('menu', MenuController::class)->middleware('check.permission:menu.index');
    Route::get('permission', [PermissionController::class, 'index'])->name('permission.index')->middleware('check.permission:permission.index');
    Route::put('permission', [PermissionController::class, 'update'])->name('permission.update')->middleware('check.permission:permission.index');

    // Activity Log
    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('activity-log/data', [ActivityLogController::class, 'getData'])->name('activity-log.data');
    Route::get('activity-log/statistics', [ActivityLogController::class, 'statistics'])->name('activity-log.statistics');

    // Website Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('check.permission:settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('check.permission:settings.index');
    Route::get('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache')->middleware('check.permission:settings.index');

    // Personal Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Impersonate Features
    Route::get('impersonate/start/{id}', [ImpersonateController::class, 'start'])->name('impersonate.start');
    Route::get('impersonate/stop', [ImpersonateController::class, 'stop'])->name('impersonate.stop');

    // System Status & Backup
    Route::get('system/health', [SystemController::class, 'health'])->name('system.health')->middleware('check.permission:system.health');
    Route::get('system/backup', [SystemController::class, 'backup'])->name('system.backup')->middleware('check.permission:system.health');
    Route::resource('jenis-surat', JenisSuratController::class)->middleware('check.permission:jenis-surat.index');
    Route::resource('berita', BeritaController::class)->middleware('check.permission:berita.index');
    Route::resource('galeri', GaleriController::class)->middleware('check.permission:galeri.index');
    Route::resource('agenda', AgendaController::class)->middleware('check.permission:agenda.index');
    Route::resource('jadwal-pelayanan', JadwalPelayananController::class)->middleware('check.permission:jadwal-pelayanan.index');
    Route::resource('kategori-pengaduan', KategoriPengaduanController::class)->middleware('check.permission:kategori-pengaduan.index');
    Route::resource('pemohon', PemohonController::class)->middleware('check.permission:pemohon.index');

    // Tiket: monitoring + status transition only (no generic create/edit/delete)
    Route::get('tiket', [TiketController::class, 'index'])->name('tiket.index')->middleware('check.permission:tiket.index');
    Route::get('tiket/{id}', [TiketController::class, 'show'])->name('tiket.show')->middleware('check.permission:tiket.index');
    Route::post('tiket/{id}/status', [TiketController::class, 'updateStatus'])->name('tiket.update-status')->middleware('check.permission:tiket.index');
    Route::get('tiket/{id}/lampiran/{mediaId}', [TiketController::class, 'downloadLampiran'])->name('tiket.lampiran')->middleware('check.permission:tiket.index');
    Route::get('tiket/{id}/surat-pdf', [TiketController::class, 'downloadSuratPdf'])->name('tiket.surat-pdf')->middleware('check.permission:tiket.index');
    Route::post('tiket/{id}/surat-pdf', [TiketController::class, 'regenerateSuratPdf'])->name('tiket.regenerate-surat-pdf')->middleware('check.permission:tiket.index');
});
