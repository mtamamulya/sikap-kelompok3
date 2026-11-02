<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PengajuanController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Halaman dosen menggunakan frontend Naya. Login tetap menjadi integrasi kelompok 1.
Route::get('/dosen/dashboard', [DosenDashboardController::class, 'index'])->name('dosen.dashboard');
Route::get('/dosen/kegiatan', [KegiatanController::class, 'index'])->name('dosen.kegiatan.index');
Route::view('/dosen/kegiatan/create', 'kegiatan.create')->name('dosen.kegiatan.create');
Route::get('/dosen/kegiatan/{id}', [KegiatanController::class, 'show'])->whereNumber('id')->name('dosen.kegiatan.show');
Route::view('/dosen/kegiatan/{id}/edit', 'kegiatan.edit')->whereNumber('id')->name('dosen.kegiatan.edit');

// Halaman Prestasi dosen sudah ada; hubungkan tampilannya tanpa menambah CRUD baru.
Route::view('/prestasi', 'prestasi.index')->name('prestasi.index');
Route::view('/prestasi/create', 'prestasi.create')->name('prestasi.create');
Route::view('/prestasi/{id}', 'prestasi.show')->whereNumber('id')->name('prestasi.show');
Route::view('/prestasi/{id}/edit', 'prestasi.edit')->whereNumber('id')->name('prestasi.edit');
// Verifikasi kegiatan (tombol mata di Dashboard). Angka = id kegiatan.
Route::view('/verifikasi/{id}', 'pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('verifikasi.show');

Route::view('/profil', 'profil.show')->name('profil');
Route::view('/notifikasi', 'notifikasi.index')->name('notifikasi');

Route::view('/penelitian', 'penelitian.index')->name('penelitian.index');
Route::view('/penelitian/create', 'penelitian.create')->name('penelitian.create');
// Detail: harus di bawah /penelitian/create supaya "create" tidak dianggap sebagai {id}
Route::view('/penelitian/{id}', 'penelitian.show')->whereNumber('id')->name('penelitian.show');
Route::view('/penelitian/{id}/edit', 'penelitian.edit')->whereNumber('id')->name('penelitian.edit');

Route::view('/pengabdian', 'pengabdian.index')->name('pengabdian.index');
Route::view('/pengabdian/create', 'pengabdian.create')->name('pengabdian.create');
// Detail: harus di bawah /pengabdian/create supaya "create" tidak dianggap sebagai {id}
Route::view('/pengabdian/{id}', 'pengabdian.show')->whereNumber('id')->name('pengabdian.show');
Route::view('/pengabdian/{id}/edit', 'pengabdian.edit')->whereNumber('id')->name('pengabdian.edit');

Route::view('/pengajaran', 'pengajaran.index')->name('pengajaran.index');
Route::view('/pengajaran/create', 'pengajaran.create')->name('pengajaran.create');
// Detail: harus di bawah /pengajaran/create supaya "create" tidak dianggap sebagai {id}
Route::view('/pengajaran/{id}', 'pengajaran.show')->whereNumber('id')->name('pengajaran.show');
Route::view('/pengajaran/{id}/edit', 'pengajaran.edit')->whereNumber('id')->name('pengajaran.edit');

Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
Route::view('/kegiatan/create', 'kegiatan.create')->name('kegiatan.create');
// Detail: harus di bawah /kegiatan/create supaya "create" tidak dianggap sebagai {id}
Route::get('/kegiatan/{id}', [KegiatanController::class, 'show'])->whereNumber('id')->name('kegiatan.show');
Route::view('/kegiatan/{id}/edit', 'kegiatan.edit')->whereNumber('id')->name('kegiatan.edit');

// Pengajuan (submenu dropdown)
Route::view('/pengajuan/dosen', 'pengajuan.dosen')->name('pengajuan.dosen');
// Detail pengajuan dosen (angka = id pengajuan). Halaman yang sama dengan detail pengajuan, tombol Kembali ke daftar Dosen.
Route::view('/pengajuan/dosen/{id}', 'pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('pengajuan.dosen.show');
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
// Detail pengajuan mahasiswa (angka = id pengajuan)
Route::view('/pengajuan/mahasiswa/{id}', 'pengajuan.show', ['asal' => 'mahasiswa'])->whereNumber('id')->name('pengajuan.mahasiswa.show');
// Tinjau / detail pengajuan (angka = id pengajuan)
// Detail umum memakai controller pengajuan; detail dosen tetap memakai frontend Copy.

// Fitur tambahan tim tetap tersedia tanpa mengganti halaman utama dari Copy.
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
