<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\DosenPengajaranController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - SIKAP Kelompok 3
|--------------------------------------------------------------------------
| Prefix otomatis "/api/v1" (via bootstrap/app.php).
| Contoh: Route::get('/ping') -> GET /api/v1/ping
*/

// Endpoint Health Check (Sesuai API-CONTRACT.md)
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'Service Kelompok 3 Aktif',
        'data' => [
            'status' => 'ok',
            'service' => 'sikap-kelompok3',
            'time' => now()->toIso8601String(),
        ],
    ], 200);
});

// Autentikasi API (token Sanctum)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('api.login');

// Rute yang membutuhkan login
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Profil user yang sedang login (dari rekan tim)
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Data profil user berhasil diambil',
            'data' => $request->user(),
        ], 200);
    });

    // Pengajaran (Kelompok 3)
    Route::get('pengajaran/opsi', [DosenPengajaranController::class, 'opsiFormApi'])->name('api.pengajaran.opsi');
    Route::apiResource('pengajaran', DosenPengajaranController::class)
        ->only(['index', 'store', 'show', 'update'])
        ->names('api.pengajaran');
});

/*
|--------------------------------------------------------------------------
| Integrasi dengan Kelompok 1 (Endpoint Kontrak)
|--------------------------------------------------------------------------
*/
Route::prefix('integrasi')
    ->middleware('auth:sanctum')
    ->group(function () {
        // TODO: Daftarkan endpoint API fitur di sini
    });

// Rute Publik (Tidak perlu login)
Route::get('/berita', [BeritaController::class, 'index']);

// Import rute API tambahan
require __DIR__.'/api-admin-kaprodi.php';
