<?php

use App\Http\Controllers\PengajuanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PengajuanController::class, 'index'])->name('pengajuan.index');
Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
Route::get('/pengajuan/{id}', [PengajuanController::class, 'show'])->whereNumber('id')->name('pengajuan.show');
Route::view('/segera', 'segera')->name('segera');