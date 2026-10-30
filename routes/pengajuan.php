<?php

use App\Http\Controllers\PengajuanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
Route::post('/pengajuan/upload', [PengajuanController::class, 'upload'])->name('pengajuan.upload');
Route::delete('/pengajuan/upload', [PengajuanController::class, 'removeUpload'])->name('pengajuan.upload.remove');
Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
Route::get('/pengajuan/{id}', [PengajuanController::class, 'show'])->whereNumber('id')->name('pengajuan.show');
Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->whereNumber('id')->name('pengajuan.edit');
Route::put('/pengajuan/{id}', [PengajuanController::class, 'update'])->whereNumber('id')->name('pengajuan.update');
Route::get('/pengajuan-segera', function (Request $request) {
    $destination = match (mb_strtolower(trim($request->query('menu', 'dashboard')))) {
        'dashboard' => 'mahasiswa.dashboard',
        'mahasiswa' => 'mahasiswa.index',
        'mata kuliah' => 'mahasiswa.mata-kuliah',
        'prestasi mahasiswa' => 'mahasiswa.prestasi',
        'kegiatan' => 'mahasiswa.kegiatan',
        'profil saya' => 'mahasiswa.profil',
        default => abort(404),
    };

    return redirect()->route($destination);
})->name('pengajuan-segera');
