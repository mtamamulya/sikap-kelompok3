<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanProdiData;
use Illuminate\Http\Request;

/**
 * Halaman "Kegiatan" (admin) gabungan: tab Dosen | Mahasiswa | Prodi.
 * Data Dosen & Mahasiswa memakai sumber yang sama dengan halaman lamanya
 * (KegiatanDosenController::index() & KegiatanMahasiswaController::index()).
 * Data Prodi masih contoh (KegiatanProdiData). TODO-DB: ganti dengan query kegiatan prodi.
 */
class AdminKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), ['dosen', 'mahasiswa', 'prodi'], true) ? $request->query('tab') : 'dosen';

        $data = [
            'dosen' => $this->ambil(KegiatanDosenController::class),
            'mahasiswa' => $this->ambil(KegiatanMahasiswaController::class),
            'prodi' => $this->prodi(),
        ];

        return view('admin.kegiatan', ['data' => $data, 'tab' => $tab]);
    }

    /** Ambil $rows dari controller daftar lama, lalu samakan nama kolomnya. */
    private function ambil(string $controller): array
    {
        $view = app($controller)->index();
        $isi = method_exists($view, 'getData') ? $view->getData() : [];

        return array_values(array_map(function ($r) {
            return [
                'id' => $r['id'] ?? '',
                'nama' => $r['nama'] ?? $r['kegiatan'] ?? '-',
                'kategori' => $r['kategori'] ?? '-',
                'pengaju' => $r['pengaju'] ?? $r['dosen'] ?? '-',
                'tanggal' => $r['tanggal'] ?? '-',
                'status' => $r['status'] ?? '-',
                'semester' => $r['semester'] ?? '',
                'kelas' => $r['kelas'] ?? '',
            ];
        }, $isi['rows'] ?? []));
    }

    /** Kegiatan tingkat program studi (data contoh, sumber sama dengan laporan PDF/Excel). */
    private function prodi(): array
    {
        return array_map(fn ($r) => [
            'id' => $r['id'],
            'nama' => $r['nama'],
            'kategori' => $r['kategori'],
            'pengaju' => $r['penanggung_jawab'],   // kolom tabel "Penanggung Jawab"
            'tanggal' => $r['tanggal'],
            'status' => $r['status'],
            'semester' => $r['semester'],
            'kelas' => '',
        ], KegiatanProdiData::semua());
    }
}
