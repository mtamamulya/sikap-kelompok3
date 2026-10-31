<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke dashboard
Route::redirect('/', '/dashboard');

// --- Halaman Utama & Fitur ---
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/kegiatan', 'kegiatan')->name('kegiatan.index');
Route::view('/profil', 'profil')->name('profil');

// Alias pendukung komponen & sidebar
Route::view('/kegiatan-page', 'kegiatan')->name('kegiatan');
Route::view('/header', 'header')->name('header');
Route::view('/navbar', 'navbar')->name('navbar');

// --- Notifikasi ---
Route::view('/notifikasi', 'notifikasi.index')->name('notifikasi');

// --- Penelitian ---
Route::view('/penelitian', 'penelitian.index')->name('penelitian.index');
Route::view('/penelitian/create', 'penelitian.create')->name('penelitian.create');
Route::view('/penelitian/{id}', 'penelitian.show')->whereNumber('id')->name('penelitian.show');
Route::view('/penelitian/{id}/edit', 'penelitian.edit')->whereNumber('id')->name('penelitian.edit');

// --- Pengabdian ---
Route::view('/pengabdian', 'pengabdian.index')->name('pengabdian.index');
Route::view('/pengabdian/create', 'pengabdian.create')->name('pengabdian.create');
Route::view('/pengabdian/{id}', 'pengabdian.show')->whereNumber('id')->name('pengabdian.show');
Route::view('/pengabdian/{id}/edit', 'pengabdian.edit')->whereNumber('id')->name('pengabdian.edit');

// --- Pengajuan ---
Route::view('/pengajuan/dosen', 'pengajuan.dosen')->name('pengajuan.dosen');
Route::view('/pengajuan/dosen/{id}', 'pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('pengajuan.dosen.show');
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
Route::view('/pengajuan/mahasiswa/{id}', 'pengajuan.show', ['asal' => 'mahasiswa'])->whereNumber('id')->name('pengajuan.mahasiswa.show');
Route::view('/pengajuan/{id}', 'pengajuan.show')->whereNumber('id')->name('pengajuan.show');

// Route pengajuan berita (diperlukan oleh Automated Test CI)
Route::view('/pengajuan/berita/tambah', 'pengajuan.berita.create')->name('pengajuan.berita.create');
Route::view('/pengajuan/berita', 'pengajuan.berita.index')->name('pengajuan.berita.index');

// --- Controller Mahasiswa ---
Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
Route::get('/mahasiswa/mata-kuliah', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'semua')->name('mahasiswa.mata-kuliah');
Route::get('/mahasiswa/mata-kuliah/semester-berjalan', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'berjalan')->name('mahasiswa.mata-kuliah.berjalan');
Route::get('/mahasiswa/mata-kuliah/riwayat', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'riwayat')->name('mahasiswa.mata-kuliah.riwayat');

Route::get('/mahasiswa/prestasi', [MahasiswaController::class, 'prestasi'])->name('mahasiswa.prestasi');
Route::get('/mahasiswa/prestasi/tambah', [MahasiswaController::class, 'prestasiTambah'])->name('mahasiswa.prestasi.tambah');
Route::post('/mahasiswa/prestasi', [MahasiswaController::class, 'prestasiSimpan'])->name('mahasiswa.prestasi.simpan');
Route::get('/mahasiswa/prestasi/{prestasi}/edit', [MahasiswaController::class, 'prestasiEdit'])->name('mahasiswa.prestasi.edit');
Route::delete('/mahasiswa/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiHapus'])->name('mahasiswa.prestasi.hapus');
Route::put('/mahasiswa/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiUpdate'])->name('mahasiswa.prestasi.update');
