<?php

/*
 * Endpoint baca (GET) untuk modul kelompok 1. File ini dimuat dari routes/api.php dengan satu baris:
 *     require __DIR__.'/api-admin-kaprodi.php';
 * Prefix /api/v1 berasal dari bootstrap/app.php (apiPrefix), jadi path di bawah TANPA /api/v1.
 * Alias kelas dipakai karena Api\BeritaController dan controller web bernama sama sudah ada.
 */

use App\Http\Controllers\Api\AkreditasiController as ApiAkreditasi;
use App\Http\Controllers\Api\KurikulumController as ApiKurikulum;
use App\Http\Controllers\Api\MataKuliahController as ApiMataKuliah;
use Illuminate\Support\Facades\Route;

Route::get('kurikulum', [ApiKurikulum::class, 'index'])->name('api.kurikulum.index');
Route::get('kurikulum/{id}', [ApiKurikulum::class, 'show'])->whereNumber('id')->name('api.kurikulum.show');
Route::get('kurikulum/{id}/mata-kuliah', [ApiKurikulum::class, 'mataKuliah'])->whereNumber('id')->name('api.kurikulum.mata-kuliah');

Route::get('mata-kuliah', [ApiMataKuliah::class, 'index'])->name('api.mata-kuliah.index');
Route::get('mata-kuliah/{id}', [ApiMataKuliah::class, 'show'])->whereNumber('id')->name('api.mata-kuliah.show');

Route::get('akreditasi', [ApiAkreditasi::class, 'index'])->name('api.akreditasi.index');
Route::get('akreditasi/aktif', [ApiAkreditasi::class, 'aktif'])->name('api.akreditasi.aktif');
Route::get('akreditasi/{id}', [ApiAkreditasi::class, 'show'])->whereNumber('id')->name('api.akreditasi.show');
