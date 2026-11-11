<?php

use App\Http\Controllers\Api\BeritaController;
// 1. Tambahkan import class controller pengajaran di sini
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

// 2. Rute yang membutuhkan login (termasuk Pengajaran) dikelompokkan di sini
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Data profil user berhasil diambil',
            'data' => $request->user(),
        ], 200);
    });

    Route::get('pengajaran/opsi', [DosenPengajaranController::class, 'opsiFormApi'])->name('api.pengajaran.opsi');
    Route::apiResource('pengajaran', DosenPengajaranController::class)
        ->only(['index', 'store', 'show', 'update'])
        ->names('api.pengajaran');
    // // --- Rute API Pengajaran (Hanya bisa diakses jika sudah login) ---
    // Route::get('pengajaran/opsi', [DosenPengajaranController::class, 'opsiFormApi']);
    // Route::apiResource('pengajaran', DosenPengajaranController::class);

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
