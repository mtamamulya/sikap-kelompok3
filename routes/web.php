<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'dashboard')->name('dashboard');

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

Route::view('/kegiatan', 'kegiatan.index')->name('kegiatan.index');
Route::view('/kegiatan/create', 'kegiatan.create')->name('kegiatan.create');
// Detail: harus di bawah /kegiatan/create supaya "create" tidak dianggap sebagai {id}
Route::view('/kegiatan/{id}', 'kegiatan.show')->whereNumber('id')->name('kegiatan.show');
Route::view('/kegiatan/{id}/edit', 'kegiatan.edit')->whereNumber('id')->name('kegiatan.edit');

// Pengajuan (submenu dropdown)
Route::view('/pengajuan/dosen', 'pengajuan.dosen')->name('pengajuan.dosen');
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
// Tinjau / detail pengajuan (angka = id pengajuan)
Route::view('/pengajuan/{id}', 'pengajuan.show')->whereNumber('id')->name('pengajuan.show');

// Route halaman Pengajuan mahasiswa (daftar, form, upload, detail) ada di routes/pengajuan.php.
// Route detail tim (/pengajuan/{id} dengan nama pengajuan.show) tidak dipakai karena bentrok
// dengan pengajuan.show milik controller di file tersebut.
require __DIR__.'/pengajuan.php';
