<?php

use App\Http\Controllers\Api\LandingApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User API Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index']);
    Route::get('/{id}', [UserApiController::class, 'show']);
});

// 3S Public Landing API Routes
Route::get('/jenis-surat', [LandingApiController::class, 'jenisSurat']);
Route::get('/jadwal-pelayanan', [LandingApiController::class, 'jadwalPelayanan']);
Route::post('/cek-status', [LandingApiController::class, 'cekStatus']);
Route::get('/berita', [LandingApiController::class, 'berita']);
Route::get('/events', [LandingApiController::class, 'events']);
