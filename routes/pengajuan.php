<?php

use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PengajuanController;
use Illuminate\Support\Facades\Route;

Route::get('/pengajuan/berita', [BeritaController::class, 'index'])->name('pengajuan.berita.index');
Route::get('/pengajuan/berita/baru', [BeritaController::class, 'create'])->name('pengajuan.berita.create');
Route::post('/pengajuan/berita', [BeritaController::class, 'store'])->name('pengajuan.berita.store');
Route::get('/pengajuan/berita/{id}', [BeritaController::class, 'show'])->whereNumber('id')->name('pengajuan.berita.show');
Route::get('/pengajuan/berita/{id}/edit', [BeritaController::class, 'edit'])->whereNumber('id')->name('pengajuan.berita.edit');
Route::put('/pengajuan/berita/{id}', [BeritaController::class, 'update'])->whereNumber('id')->name('pengajuan.berita.update');

Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
Route::get('/pengajuan/{id}', [PengajuanController::class, 'show'])->whereNumber('id')->name('pengajuan.show');
Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->whereNumber('id')->name('pengajuan.edit');
Route::put('/pengajuan/{id}', [PengajuanController::class, 'update'])->whereNumber('id')->name('pengajuan.update');
Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->whereNumber('id')->name('pengajuan.destroy');
Route::get('/pengajuan-segera', function () {
    if (! request()->has('menu')) {
        return view('PengajuanMahasiswa.segera');
    }

    $destination = match (mb_strtolower(trim((string) request()->query('menu')))) {
        'dashboard' => 'mahasiswa.dashboard',
        'mahasiswa' => 'mahasiswa.index',
        'mata kuliah' => 'mahasiswa.mata-kuliah',
        'prestasi', 'prestasi mahasiswa' => 'mahasiswa.prestasi',
        'kegiatan' => 'mahasiswa.kegiatan',
        'profil saya' => 'mahasiswa.profil',
        default => abort(404),
    };

    return redirect()->route($destination);
})->name('pengajuan-segera');
