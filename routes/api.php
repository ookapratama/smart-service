<?php

use App\Http\Controllers\Api\DeployWebhookController;
use App\Http\Controllers\Api\LandingApiController;
use Illuminate\Support\Facades\Route;

Route::post('/deploy/webhook', [DeployWebhookController::class, 'run']);

// 3S Public Landing API Routes
Route::get('/jenis-surat', [LandingApiController::class, 'jenisSurat']);
Route::get('/jadwal-pelayanan', [LandingApiController::class, 'jadwalPelayanan']);
Route::post('/cek-status', [LandingApiController::class, 'cekStatus']);
Route::get('/berita', [LandingApiController::class, 'berita']);
Route::get('/events', [LandingApiController::class, 'events']);
