<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::view('/dashboard', 'dosen.dashboard')->name('dashboard');
// Verifikasi kegiatan (tombol mata di Dashboard). Angka = id kegiatan.
Route::view('/verifikasi/{id}', 'dosen.pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('verifikasi.show');

Route::view('/profil', 'dosen.profil.show')->name('profil');
Route::view('/notifikasi', 'dosen.notifikasi.index')->name('notifikasi');

Route::view('/penelitian', 'dosen.penelitian.index')->name('penelitian.index');
Route::view('/penelitian/create', 'dosen.penelitian.create')->name('penelitian.create');
// Detail: harus di bawah /penelitian/create supaya "create" tidak dianggap sebagai {id}
Route::view('/penelitian/{id}', 'dosen.penelitian.show')->whereNumber('id')->name('penelitian.show');
Route::view('/penelitian/{id}/edit', 'dosen.penelitian.edit')->whereNumber('id')->name('penelitian.edit');

Route::view('/pengabdian', 'dosen.pengabdian.index')->name('pengabdian.index');
Route::view('/pengabdian/create', 'dosen.pengabdian.create')->name('pengabdian.create');
// Detail: harus di bawah /pengabdian/create supaya "create" tidak dianggap sebagai {id}
Route::view('/pengabdian/{id}', 'dosen.pengabdian.show')->whereNumber('id')->name('pengabdian.show');
Route::view('/pengabdian/{id}/edit', 'dosen.pengabdian.edit')->whereNumber('id')->name('pengabdian.edit');

Route::view('/pengajaran', 'dosen.pengajaran.index')->name('pengajaran.index');
Route::view('/pengajaran/create', 'dosen.pengajaran.create')->name('pengajaran.create');
// Detail: harus di bawah /pengajaran/create supaya "create" tidak dianggap sebagai {id}
Route::view('/pengajaran/{id}', 'dosen.pengajaran.show')->whereNumber('id')->name('pengajaran.show');
Route::view('/pengajaran/{id}/edit', 'dosen.pengajaran.edit')->whereNumber('id')->name('pengajaran.edit');

Route::view('/prestasi', 'dosen.prestasi.index')->name('prestasi.index');
Route::view('/prestasi/create', 'dosen.prestasi.create')->name('prestasi.create');
// Detail: harus di bawah /prestasi/create supaya "create" tidak dianggap sebagai {id}
Route::view('/prestasi/{id}', 'dosen.prestasi.show')->whereNumber('id')->name('prestasi.show');
Route::view('/prestasi/{id}/edit', 'dosen.prestasi.edit')->whereNumber('id')->name('prestasi.edit');

Route::view('/kegiatan', 'dosen.kegiatan.index')->name('kegiatan.index');
Route::view('/kegiatan/create', 'dosen.kegiatan.create')->name('kegiatan.create');
// Detail: harus di bawah /kegiatan/create supaya "create" tidak dianggap sebagai {id}
Route::view('/kegiatan/{id}', 'dosen.kegiatan.show')->whereNumber('id')->name('kegiatan.show');
Route::view('/kegiatan/{id}/edit', 'dosen.kegiatan.edit')->whereNumber('id')->name('kegiatan.edit');

Route::view('/mahasiswa/dashboard', 'mahasiswa.Dashboard.dashboard')->name('mahasiswa.dashboard');
Route::get('/mahasiswa', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.index');
Route::get('/mahasiswa/mata-kuliah/{tab?}', [MahasiswaController::class, 'mataKuliah'])->name('mahasiswa.mata-kuliah');
Route::get('/mahasiswa/prestasi', [MahasiswaController::class, 'prestasi'])->name('mahasiswa.prestasi');
Route::get('/mahasiswa/prestasi/tambah', [MahasiswaController::class, 'prestasiTambah'])->name('mahasiswa.prestasi.tambah');
Route::post('/mahasiswa/prestasi', [MahasiswaController::class, 'prestasiSimpan'])->name('mahasiswa.prestasi.simpan');
Route::get('/mahasiswa/prestasi/{prestasi}/edit', [MahasiswaController::class, 'prestasiEdit'])->name('mahasiswa.prestasi.edit');
Route::put('/mahasiswa/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiUpdate'])->name('mahasiswa.prestasi.update');
Route::delete('/mahasiswa/prestasi/{prestasi}', [MahasiswaController::class, 'prestasiHapus'])->name('mahasiswa.prestasi.hapus');
Route::view('/mahasiswa/kegiatan', 'mahasiswa.kegiatan')->name('mahasiswa.kegiatan');
Route::view('/mahasiswa/profil', 'mahasiswa.profil')->name('mahasiswa.profil');

// Pengajuan (submenu dropdown)
Route::view('/pengajuan/dosen', 'dosen.pengajuan.dosen')->name('pengajuan.dosen');
// Detail pengajuan dosen (angka = id pengajuan). Halaman yang sama dengan detail pengajuan, tombol Kembali ke daftar Dosen.
Route::view('/pengajuan/dosen/{id}', 'dosen.pengajuan.show', ['asal' => 'dosen'])->whereNumber('id')->name('pengajuan.dosen.show');
Route::view('/pengajuan/mahasiswa', 'dosen.pengajuan.mahasiswa')->name('pengajuan.mahasiswa');
// Detail pengajuan mahasiswa (angka = id pengajuan)
Route::view('/pengajuan/mahasiswa/{id}', 'dosen.pengajuan.show', ['asal' => 'mahasiswa'])->whereNumber('id')->name('pengajuan.mahasiswa.show');
// Pengajuan berita dosen (angka = id berita). Admin yang mereview dikerjakan terpisah.
Route::view('/pengajuan/berita', 'dosen.berita.index')->name('berita.index');
Route::view('/pengajuan/berita/create', 'dosen.berita.create')->name('berita.create');
// Detail: harus di bawah /pengajuan/berita/create supaya "create" tidak dianggap sebagai {id}
Route::view('/pengajuan/berita/{id}', 'dosen.berita.show')->whereNumber('id')->name('berita.show');
Route::view('/pengajuan/berita/{id}/edit', 'dosen.berita.edit')->whereNumber('id')->name('berita.edit');

require __DIR__.'/pengajuan.php';