<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Mahasiswa
|--------------------------------------------------------------------------
| View yang dipakai (resources/views/mahasiswa/):
|   dashboard-mhs.blade.php, mata-kuliah.blade.php,
|   prestasi.blade.php, prestasi-tambah.blade.php
|
| Semua URL diawali /mahasiswa dan semua nama route diawali "mahasiswa.".
| Jika perlu login, tambahkan ->middleware('auth') pada Route::prefix di bawah.
*/

Route::prefix('mahasiswa')->group(function () {

    // /mahasiswa -> langsung ke dashboard mahasiswa
    Route::redirect('/', '/mahasiswa/dashboard')->name('mahasiswa.index');

    // ----- Dashboard -----
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])
        ->name('mahasiswa.dashboard');

    // ----- Mata kuliah (3 tab; tiap tab punya route sendiri supaya tombol tab bisa berpindah halaman) -----
    // Menu sidebar "Mata kuliah" -> langsung membuka tab "Semester berjalan"
    Route::get('/mata-kuliah', [MahasiswaController::class, 'mataKuliah'])
        ->defaults('tab', 'berjalan')
        ->name('mahasiswa.mata-kuliah');

    // Tab "Semester berjalan"
    Route::get('/mata-kuliah/semester-berjalan', [MahasiswaController::class, 'mataKuliah'])
        ->defaults('tab', 'berjalan')
        ->name('mahasiswa.mata-kuliah.berjalan');

    // Tab "Riwayat mata kuliah"
    Route::get('/mata-kuliah/riwayat', [MahasiswaController::class, 'mataKuliah'])
        ->defaults('tab', 'semua')
        ->name('mahasiswa.mata-kuliah.riwayat');

    // Tab "Semua mata kuliah" (Tingkat 1 - 4)
    Route::get('/mata-kuliah/katalog', [MahasiswaController::class, 'mataKuliah'])
        ->defaults('tab', 'katalog')
        ->name('mahasiswa.mata-kuliah.katalog');

    // ----- Prestasi -----
    Route::get('/prestasi', [MahasiswaController::class, 'prestasi'])
        ->name('mahasiswa.prestasi');

    // "tambah" harus di atas route yang memakai {prestasi}
    Route::get('/prestasi/tambah', [MahasiswaController::class, 'prestasiTambah'])
        ->name('mahasiswa.prestasi.tambah');

    Route::post('/prestasi', [MahasiswaController::class, 'prestasiSimpan'])
        ->name('mahasiswa.prestasi.simpan');

    Route::get('/prestasi/{prestasi}/edit', [MahasiswaController::class, 'prestasiEdit'])
        ->name('mahasiswa.prestasi.edit');

    Route::put('/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiUpdate'])
        ->name('mahasiswa.prestasi.update');

    Route::delete('/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiHapus'])
        ->name('mahasiswa.prestasi.hapus');
});
