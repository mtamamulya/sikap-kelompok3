<?php

use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\KegiatanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'Service Kelompok 3 Aktif',
    ], 200);
});

Route::get('/berita', [BeritaController::class, 'index']);

Route::prefix('v1')->group(function () {
    Route::get('/kegiatan', [KegiatanController::class, 'index']);
    Route::get('/kegiatan/{id}', [KegiatanController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/kegiatan', [KegiatanController::class, 'store']);
        Route::put('/kegiatan/{id}', [KegiatanController::class, 'update']);
        Route::delete('/kegiatan/{id}', [KegiatanController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ], 200);
    });
});