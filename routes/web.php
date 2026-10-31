<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama langsung ke dashboard
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
// --- Halaman Utama (Langsung memanggil file di root folder resources/views) ---
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/kegiatan', 'kegiatan')->name('kegiatan.index');
Route::view('/profil', 'profil')->name('profil');

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
Route::view('/pengajuan/mahasiswa', 'pengajuan.mahasiswa')->name('pengajuan.mahasiswa');

// --- Mahasiswa Controller ---
Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
Route::get('/mahasiswa/mata-kuliah', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'semua')->name('mahasiswa.mata-kuliah');
Route::get('/mahasiswa/mata-kuliah/semester-berjalan', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'berjalan')->name('mahasiswa.mata-kuliah.berjalan');
Route::get('/mahasiswa/mata-kuliah/riwayat', [MahasiswaController::class, 'mataKuliah'])->defaults('tab', 'riwayat')->name('mahasiswa.mata-kuliah.riwayat');

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
// Berita dosen menggunakan URL tersendiri; /pengajuan/berita dipakai alur berita mahasiswa.
Route::view('/berita', 'dosen.berita.index')->name('berita.index');
Route::view('/berita/create', 'dosen.berita.create')->name('berita.create');
Route::view('/berita/{id}/edit', 'dosen.berita.edit')->whereNumber('id')->name('berita.edit');
Route::view('/berita/{id}', 'dosen.berita.show')->whereNumber('id')->name('berita.show');

require __DIR__.'/pengajuan.php';
// --- Auth Dummy ---
Route::post('/logout', fn () => redirect()->route('profil'))->name('logout');
Route::post('/password/otp', fn () => back())->name('password.otp.send');
