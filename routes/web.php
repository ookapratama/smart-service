<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\KategoriPengaduanController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard as home page
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // User CRUD routes
    Route::resource('user', UserController::class)->middleware('check.permission:user.index');

    // Role & Menu Management
    Route::resource('role', RoleController::class)->middleware('check.permission:role.index');
    Route::resource('menu', MenuController::class)->middleware('check.permission:menu.index');
    Route::get('permission', [PermissionController::class, 'index'])->name('permission.index')->middleware('check.permission:permission.index');
    Route::put('permission', [PermissionController::class, 'update'])->name('permission.update')->middleware('check.permission:permission.index');

    // Products CRUD routes
    Route::get('products/export/excel', [ProductsController::class, 'exportExcel'])->name('products.export.excel')->middleware('check.permission:products.index');
    Route::get('products/export/pdf', [ProductsController::class, 'exportPdf'])->name('products.export.pdf')->middleware('check.permission:products.index');
    Route::post('products/import/excel', [ProductsController::class, 'importExcel'])->name('products.import.excel')->middleware('check.permission:products.index');
    Route::resource('products', ProductsController::class)->middleware('check.permission:products.index');

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
    Route::resource('instansi', InstansiController::class)->middleware('check.permission:instansi.index');
    Route::post('instansi/wilayah/sync', [InstansiController::class, 'syncWilayah'])->name('instansi.wilayah-sync')->middleware('check.permission:instansi.index');
    Route::resource('jenis-surat', JenisSuratController::class)->middleware('check.permission:jenis-surat.index');
    Route::resource('kategori-pengaduan', KategoriPengaduanController::class)->middleware('check.permission:kategori-pengaduan.index');
    Route::resource('pemohon', PemohonController::class)->middleware('check.permission:pemohon.index');

    // Tiket: monitoring + status transition only (no generic create/edit/delete)
    Route::get('tiket', [TiketController::class, 'index'])->name('tiket.index')->middleware('check.permission:tiket.index');
    Route::get('tiket/{id}', [TiketController::class, 'show'])->name('tiket.show')->middleware('check.permission:tiket.index');
    Route::post('tiket/{id}/status', [TiketController::class, 'updateStatus'])->name('tiket.update-status')->middleware('check.permission:tiket.index');

    // Wilayah: cascading dropdown data (read-only reference, no separate permission)
    Route::get('wilayah/{parentCode}/children', [WilayahController::class, 'children'])
        ->name('wilayah.children')
        ->where('parentCode', '[a-zA-Z0-9.]+');
});
