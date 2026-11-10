<?php

use App\Http\Controllers\Api\BeritaController;
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

/*
|--------------------------------------------------------------------------
| Integrasi dengan Kelompok 1 (Endpoint Kontrak)
|--------------------------------------------------------------------------
*/
// TODO: Daftarkan endpoint API fitur di sini

Route::get('/berita', [BeritaController::class, 'index']);
require __DIR__.'/api-admin-kaprodi.php';