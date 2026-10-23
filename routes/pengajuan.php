<?php

use App\Http\Controllers\PengajuanController;
use Illuminate\Support\Facades\Route;

Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
Route::post('/pengajuan/upload', [PengajuanController::class, 'upload'])->name('pengajuan.upload');
Route::delete('/pengajuan/upload', [PengajuanController::class, 'removeUpload'])->name('pengajuan.upload.remove');
Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
Route::get('/pengajuan/{id}', [PengajuanController::class, 'show'])->whereNumber('id')->name('pengajuan.show');
Route::view('/pengajuan-segera', 'pengajuan.segera')->name('pengajuan-segera');
require __DIR__.'/pengajuan.php';