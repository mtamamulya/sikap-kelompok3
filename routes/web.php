<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

// --- 1. KODINGAN BAWAAN DARI BRANCH ---
Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'dashboard')->name('dashboard');

// Route profil bawaan branch
Route::view('/profil', 'profil.show')->name('profil');
Route::view('/notifikasi', 'notifikasi.index')->name('notifikasi');

Route::view('/penelitian', 'penelitian.index')->name('penelitian.index');
Route::view('/penelitian/create', 'penelitian.create')->name('penelitian.create');
Route::view('/penelitian/{id}', 'penelitian.show')->whereNumber('id')->name('penelitian.show');
Route::view('/penelitian/{id}/edit', 'penelitian.edit')->whereNumber('id')->name('penelitian.edit');

Route::view('/pengabdian', 'pengabdian.index')->name('pengabdian.index');
Route::view('/pengabdian/create', 'pengabdian.create')->name('pengabdian.create');
Route::view('/pengabdian/{id}', 'pengabdian.show')->whereNumber('id')->name('pengabdian.show');
Route::view('/pengabdian/{id}/edit', 'pengabdian.edit')->whereNumber('id')->name('pengabdian.edit');

Route::view('/pengajaran', 'pengajaran.index')->name('pengajaran.index');
Route::view('/pengajaran/create', 'pengajaran.create')->name('pengajaran.create');
Route::view('/pengajaran/{id}', 'pengajaran.show')->whereNumber('id')->name('pengajaran.show');
Route::view('/pengajaran/{id}/edit', 'pengajaran.edit')->whereNumber('id')->name('pengajaran.edit');

Route::view('/prestasi', 'prestasi.index')->name('prestasi.index');
Route::view('/prestasi/create', 'prestasi.create')->name('prestasi.create');
// Detail: harus di bawah /prestasi/create supaya "create" tidak dianggap sebagai {id}
Route::view('/prestasi/{id}', 'prestasi.show')->whereNumber('id')->name('prestasi.show');
Route::view('/prestasi/{id}/edit', 'prestasi.edit')->whereNumber('id')->name('prestasi.edit');

Route::view('/kegiatan', 'kegiatan.index')->name('kegiatan.index');
Route::view('/kegiatan/create', 'kegiatan.create')->name('kegiatan.create');
Route::view('/kegiatan/{id}', 'kegiatan.show')->whereNumber('id')->name('kegiatan.show');
Route::view('/kegiatan/{id}/edit', 'kegiatan.edit')->whereNumber('id')->name('kegiatan.edit');

Route::view('/pengajuan/dosen', 'pengajuan.dosen')->name('pengajuan.dosen');
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
// --- 2. KODINGAN TAMBAHAN BUATAN ANDA ---
Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard'])
    ->name('mahasiswa.dashboard');

Route::post('/logout', fn () => redirect()->route('profil'))->name('logout');
Route::post('/password/otp', fn () => back())->name('password.otp.send');

/*
Catatan: Route '/profil' buatan Anda yang sebelumnya ada di stash,
sementara saya hilangkan karena bentrok (duplikat) dengan Route '/profil' bawaan branch di atas.
*/

// Tinjau / detail pengajuan (angka = id pengajuan)
Route::view('/pengajuan/{id}', 'pengajuan.show')->whereNumber('id')->name('pengajuan.show');