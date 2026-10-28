<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'dashboard')->name('dashboard');
// Verifikasi kegiatan (tombol mata di Dashboard). Angka = id kegiatan.
Route::view('/verifikasi/{id}', 'verifikasi.show')->whereNumber('id')->name('verifikasi.show');

Route::view('/profil', 'profil.show')->name('profil');
Route::view('/notifikasi', 'notifikasi.index')->name('notifikasi');

Route::view('/penelitian','penelitian.index')->name('penelitian.index');
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

Route::view('/prestasi', 'prestasi.index')->name('prestasi.index');
Route::view('/prestasi/create', 'prestasi.create')->name('prestasi.create');
// Detail: harus di bawah /prestasi/create supaya "create" tidak dianggap sebagai {id}
Route::view('/prestasi/{id}', 'prestasi.show')->whereNumber('id')->name('prestasi.show');
Route::view('/prestasi/{id}/edit', 'prestasi.edit')->whereNumber('id')->name('prestasi.edit');

Route::view('/kegiatan', 'kegiatan.index')->name('kegiatan.index');
Route::view('/kegiatan/create', 'kegiatan.create')->name('kegiatan.create');
// Detail: harus di bawah /kegiatan/create supaya "create" tidak dianggap sebagai {id}
Route::view('/kegiatan/{id}', 'kegiatan.show')->whereNumber('id')->name('kegiatan.show');
Route::view('/kegiatan/{id}/edit', 'kegiatan.edit')->whereNumber('id')->name('kegiatan.edit');

// Pengajuan (submenu dropdown)
Route::view('/pengajuan/dosen', 'pengajuan.dosen')->name('pengajuan.dosen');
// Detail pengajuan dosen (angka = id pengajuan). Halaman yang sama dengan detail pengajuan, tombol Kembali ke daftar Dosen.
Route::view('/pengajuan/dosen/{id}', 'pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('pengajuan.dosen.show');
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
// Detail pengajuan mahasiswa (angka = id pengajuan)
Route::view('/pengajuan/mahasiswa/{id}', 'pengajuan.show', ['asal' => 'mahasiswa'])->whereNumber('id')->name('pengajuan.mahasiswa.show');
// Tinjau / detail pengajuan (angka = id pengajuan)
Route::view('/pengajuan/{id}', 'pengajuan.show')->whereNumber('id')->name('pengajuan.show');