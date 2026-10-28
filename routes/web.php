<?php

use Illuminate\Support\Facades\Route;

// Redirect halaman utama langsung ke dashboard
Route::redirect('/', '/dashboard');

// --- Halaman Utama (Langsung memanggil file di root folder resources/views) ---
Route::view('/dashboard', 'dashboard')->name('dashboard');
<<<<<<< HEAD
Route::view('/kegiatan', 'kegiatan')->name('kegiatan.index');
Route::view('/profil', 'profil')->name('profil');
=======
// Verifikasi kegiatan (tombol mata di Dashboard). Angka = id kegiatan.
Route::view('/verifikasi/{id}', 'verifikasi.show')->whereNumber('id')->name('verifikasi.show');
>>>>>>> de7ce8142a7cef2d03c1a04b8f5470b1c127f847

// Alias pendukung agar tidak error nama route di komponen sidebar lain
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

// --- Pengajaran ---
Route::view('/pengajaran', 'pengajaran.index')->name('pengajaran.index');
Route::view('/pengajaran/create', 'pengajaran.create')->name('pengajaran.create');
Route::view('/pengajaran/{id}', 'pengajaran.show')->whereNumber('id')->name('pengajaran.show');
Route::view('/pengajaran/{id}/edit', 'pengajaran.edit')->whereNumber('id')->name('pengajaran.edit');

// --- Submenu CRUD Kegiatan ---
Route::view('/kegiatan/create', 'Kegiatan.create')->name('kegiatan.create');
Route::view('/kegiatan/{id}', 'Kegiatan.show')->whereNumber('id')->name('kegiatan.show');
Route::view('/kegiatan/{id}/edit', 'Kegiatan.edit')->whereNumber('id')->name('kegiatan.edit');

// --- Pengajuan ---
Route::view('/pengajuan/dosen', 'pengajuan.dosen')->name('pengajuan.dosen');
// Detail pengajuan dosen (angka = id pengajuan). Halaman yang sama dengan detail pengajuan, tombol Kembali ke daftar Dosen.
Route::view('/pengajuan/dosen/{id}', 'pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('pengajuan.dosen.show');
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
<<<<<<< HEAD

// --- Mahasiswa Controller ---
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

// --- Auth Dummy ---
Route::post('/logout', fn () => redirect()->route('profil'))->name('logout');
Route::post('/password/otp', fn () => back())->name('password.otp.send');
=======
// Detail pengajuan mahasiswa (angka = id pengajuan)
Route::view('/pengajuan/mahasiswa/{id}', 'pengajuan.show', ['asal' => 'mahasiswa'])->whereNumber('id')->name('pengajuan.mahasiswa.show');
// Tinjau / detail pengajuan (angka = id pengajuan)
Route::view('/pengajuan/{id}', 'pengajuan.show')->whereNumber('id')->name('pengajuan.show');
>>>>>>> de7ce8142a7cef2d03c1a04b8f5470b1c127f847
