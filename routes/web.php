<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\LandingMahasiswaController;
use Illuminate\Support\Facades\Route;

// Ganti redirect otomatis ini dengan Landing Page TRK
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::redirect('/dashboard', '/mahasiswa/dashboard')->name('dashboard');
Route::get('/dosen/dashboard', [DosenDashboardController::class, 'index'])->name('dosen.dashboard');

Route::get('/dosen/kegiatan', [KegiatanController::class, 'index'])->name('dosen.kegiatan.index');
Route::view('/dosen/kegiatan/create', 'dosen.kegiatan.create')->name('dosen.kegiatan.create');
Route::get('/dosen/kegiatan/{id}', [KegiatanController::class, 'show'])->whereNumber('id')->name('dosen.kegiatan.show');
Route::view('/dosen/kegiatan/{id}/edit', 'dosen.kegiatan.edit')->whereNumber('id')->name('dosen.kegiatan.edit');

Route::view('/prestasi', 'dosen.prestasi.index')->name('prestasi.index');
Route::view('/prestasi/create', 'dosen.prestasi.create')->name('prestasi.create');
Route::view('/prestasi/{id}', 'dosen.prestasi.show')->whereNumber('id')->name('prestasi.show');
Route::view('/prestasi/{id}/edit', 'dosen.prestasi.edit')->whereNumber('id')->name('prestasi.edit');
Route::view('/verifikasi/{id}', 'pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('verifikasi.show');

Route::view('/profil', 'dosen.profil.show')->name('profil');
Route::view('/notifikasi', 'dosen.notifikasi.index')->name('notifikasi');

Route::view('/penelitian', 'dosen.penelitian.index')->name('penelitian.index');
Route::view('/penelitian/create', 'dosen.penelitian.create')->name('penelitian.create');
Route::view('/penelitian/{id}', 'dosen.penelitian.show')->whereNumber('id')->name('penelitian.show');
Route::view('/penelitian/{id}/edit', 'dosen.penelitian.edit')->whereNumber('id')->name('penelitian.edit');

Route::view('/pengabdian', 'dosen.pengabdian.index')->name('pengabdian.index');
Route::view('/pengabdian/create', 'dosen.pengabdian.create')->name('pengabdian.create');
Route::view('/pengabdian/{id}', 'dosen.pengabdian.show')->whereNumber('id')->name('pengabdian.show');
Route::view('/pengabdian/{id}/edit', 'dosen.pengabdian.edit')->whereNumber('id')->name('pengabdian.edit');

Route::view('/pengajaran', 'dosen.pengajaran.index')->name('pengajaran.index');
Route::view('/pengajaran/create', 'dosen.pengajaran.create')->name('pengajaran.create');
Route::view('/pengajaran/{id}', 'dosen.pengajaran.show')->whereNumber('id')->name('pengajaran.show');
Route::view('/pengajaran/{id}/edit', 'dosen.pengajaran.edit')->whereNumber('id')->name('pengajaran.edit');

Route::redirect('/kegiatan', '/mahasiswa/kegiatan');
Route::redirect('/kegiatan/create', '/mahasiswa/kegiatan/create');
Route::view('/mahasiswa/kegiatan', 'mahasiswa.kegiatan.index')->name('kegiatan.index');
Route::view('/mahasiswa/kegiatan/create', 'dosen.kegiatan.create')->name('kegiatan.create');

// Mengirimkan semua variabel dummy yang dibutuhkan kegiatan.blade.php
Route::view('/mahasiswa/kegiatan/{id}/edit', 'kegiatan', [
    'allActivities' => [],
    'categories' => [],
    'activities' => [],
])->whereNumber('id')->name('kegiatan.edit');

Route::get('/mahasiswa/kegiatan/{id}', [KegiatanController::class, 'show'])->whereNumber('id')->name('kegiatan.show');

Route::get('/pengajuan/dosen/baru', [PengajuanController::class, 'createDosen'])->name('pengajuan.dosen.create');
Route::post('/pengajuan/dosen', [PengajuanController::class, 'storeDosen'])->name('pengajuan.dosen.store');
Route::view('/pengajuan/dosen', 'dosen.pengajuan.dosen')->name('pengajuan.dosen');
Route::view('/pengajuan/dosen/{id}', 'dosen.pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('pengajuan.dosen.show');
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
Route::view('/pengajuan/mahasiswa/{id}', 'dosen.pengajuan.show', ['asal' => 'mahasiswa'])->whereNumber('id')->name('pengajuan.mahasiswa.show');

Route::view('/berita', 'dosen.berita.index')->name('berita.index');
Route::view('/berita/create', 'dosen.berita.create')->name('berita.create');
Route::view('/berita/{id}', 'dosen.berita.show')->whereNumber('id')->name('berita.show');
Route::view('/berita/{id}/edit', 'dosen.berita.edit')->whereNumber('id')->name('berita.edit');

Route::view('/kegiatan-page', 'kegiatan')->name('kegiatan');
Route::view('/header', 'header')->name('header');
Route::view('/navbar', 'navbar')->name('navbar');

Route::get('/mahasiswa/pengajuan/dashboard', [PengajuanController::class, 'dashboard'])->name('pengajuan.dashboard');
Route::redirect('/mahasiswa', '/mahasiswa/mahasiswa/dashboard');
Route::redirect('/mahasiswa/akademik', '/mahasiswa/mahasiswa/dashboard');
Route::get('/mahasiswa/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.index');

Route::get('/mahasiswa/dashboard', [DashboardController::class, 'index'])->name('mahasiswa.dashboard');
Route::get('/mahasiswa/mata-kuliah', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'berjalan')->name('mahasiswa.mata-kuliah');
Route::get('/mahasiswa/mata-kuliah/semester-berjalan', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'berjalan')->name('mahasiswa.mata-kuliah.berjalan');
Route::get('/mahasiswa/mata-kuliah/katalog', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'katalog')->name('mahasiswa.mata-kuliah.katalog');
Route::get('/mahasiswa/mata-kuliah/riwayat', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'semua')->name('mahasiswa.mata-kuliah.riwayat');
Route::view('/mahasiswa/profil', 'mahasiswa.mahasiswa.profil')->name('mahasiswa.profil');
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/prestasi', [MahasiswaController::class, 'prestasi'])
        ->name('prestasi');

    // Letakkan route statis sebelum route parameter prestasi.
    Route::get('/prestasi/tambah', [MahasiswaController::class, 'prestasiTambah'])
        ->name('prestasi.tambah');
    Route::post('/prestasi', [MahasiswaController::class, 'prestasiSimpan'])
        ->name('prestasi.simpan');

    Route::get('/prestasi/{prestasi}/edit', [MahasiswaController::class, 'prestasiEdit'])
        ->name('prestasi.edit');
    Route::put('/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiUpdate'])
        ->name('prestasi.update');
    Route::delete('/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiHapus'])
        ->name('prestasi.hapus');
});

require __DIR__.'/pengajuan.php';

Route::redirect('/pengajuan', '/mahasiswa/pengajuan');
Route::redirect('/pengajuan/baru', '/mahasiswa/pengajuan/baru');
Route::redirect('/pengajuan/dashboard', '/mahasiswa/pengajuan/dashboard');
Route::redirect('/pengajuan/berita', '/mahasiswa/pengajuan/berita');
Route::redirect('/pengajuan/berita/baru', '/mahasiswa/pengajuan/berita/baru');
Route::get('/pengajuan/berita/{id}/edit', fn ($id) => redirect()->route('pengajuan.berita.edit', $id))->whereNumber('id');
Route::get('/pengajuan/berita/{id}', fn ($id) => redirect()->route('pengajuan.berita.show', $id))->whereNumber('id');
Route::get('/pengajuan/{id}/edit', fn ($id) => redirect()->route('pengajuan.edit', $id))->whereNumber('id');
Route::get('/pengajuan/{id}', fn ($id) => redirect()->route('pengajuan.show', $id))->whereNumber('id');
Route::get('/kegiatan/{id}/edit', fn ($id) => redirect()->route('kegiatan.edit', $id))->whereNumber('id');
Route::get('/kegiatan/{id}', fn ($id) => redirect()->route('kegiatan.show', $id))->whereNumber('id');

// Route Sinkronisasi Kelas
Route::post('/admin/mahasiswa/buat-kelas', [LandingMahasiswaController::class, 'buatKelasSemester'])->name('admin.mahasiswa.buat-kelas');
// Route Hapus Mahasiswa (Method DELETE)
Route::delete('/admin/mahasiswa/{id}', [LandingMahasiswaController::class, 'destroy'])->name('admin.mahasiswa.destroy');

require __DIR__.'/admin-kaprodi.php';
