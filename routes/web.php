<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PengajuanController;
use Illuminate\Support\Facades\Route;

// Ganti redirect otomatis ini dengan Landing Page TRK
Route::view('/', 'home')->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
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

Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
Route::view('/kegiatan/create', 'dosen.kegiatan.create')->name('kegiatan.create');

// Mengirimkan semua variabel dummy yang dibutuhkan kegiatan.blade.php
Route::view('/kegiatan/{id}', 'kegiatan', [
    'allActivities' => [],
    'categories' => [],
    'activities' => [],
])->whereNumber('id')->name('kegiatan.show');

Route::view('/kegiatan/{id}/edit', 'kegiatan', [
    'allActivities' => [],
    'categories' => [],
    'activities' => [],
])->whereNumber('id')->name('kegiatan.edit');

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

Route::get('/pengajuan/dashboard', [PengajuanController::class, 'dashboard'])->name('pengajuan.dashboard');
Route::get('/mahasiswa', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.index');

Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
Route::get('/mahasiswa/mata-kuliah', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'semua')->name('mahasiswa.mata-kuliah');
Route::get('/mahasiswa/mata-kuliah/semester-berjalan', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'berjalan')->name('mahasiswa.mata-kuliah.berjalan');
Route::get('/mahasiswa/mata-kuliah/riwayat', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'riwayat')->name('mahasiswa.mata-kuliah.riwayat');
Route::view('/mahasiswa/kegiatan', 'mahasiswa.kegiatan')->name('mahasiswa.kegiatan');
Route::view('/mahasiswa/profil', 'mahasiswa.profil')->name('mahasiswa.profil');
Route::get('/mahasiswa/prestasi', [MahasiswaController::class, 'prestasi'])->name('mahasiswa.prestasi');
Route::get('/mahasiswa/prestasi/tambah', [MahasiswaController::class, 'prestasiTambah'])->name('mahasiswa.prestasi.tambah');
Route::post('/mahasiswa/prestasi', [MahasiswaController::class, 'prestasiSimpan'])->name('mahasiswa.prestasi.simpan');
Route::get('/mahasiswa/prestasi/{prestasi}/edit', [MahasiswaController::class, 'prestasiEdit'])->name('mahasiswa.prestasi.edit');
Route::delete('/mahasiswa/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiHapus'])->name('mahasiswa.prestasi.hapus');
Route::put('/mahasiswa/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiUpdate'])->name('mahasiswa.prestasi.update');

require __DIR__.'/pengajuan.php';
require __DIR__.'/admin-kaprodi.php';
