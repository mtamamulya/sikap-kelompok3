<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - SIKAP Kelompok 3
|--------------------------------------------------------------------------
|
| Semua route di file ini otomatis mendapat prefix "/api/v1"
| (lihat bootstrap/app.php -> apiPrefix).
|
| Contoh: Route::get('/ping') -> GET /api/v1/ping
|
| Route yang dikonsumsi oleh Kelompok 1 (database terpisah) letakkan di
| dalam group "integrasi" di bawah, supaya jelas mana kontrak publik kita.
|
*/

Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'sikap-kelompok3',
        'time' => now()->toIso8601String(),
    ]);
});

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Integrasi dengan Kelompok 1
|--------------------------------------------------------------------------
| Endpoint di bawah ini adalah KONTRAK. Jangan ubah tanpa koordinasi,
| karena Kelompok 1 memanggilnya dari aplikasi mereka.
| Dokumentasi kontrak: docs/API-CONTRACT.md
*/

Route::prefix('integrasi')
    ->middleware('auth:sanctum')
    ->group(function () {
        // TODO: diisi setelah struktur database final.
    });
