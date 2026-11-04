<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Halaman "Kegiatan" (admin) gabungan: tab Dosen | Mahasiswa | Prodi.
 * Data Dosen & Mahasiswa memakai sumber yang sama dengan halaman lamanya
 * (KegiatanDosenController::index() & KegiatanMahasiswaController::index()).
 * Data Prodi masih contoh. TODO-DB: ganti dengan query kegiatan prodi.
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

    /** Kegiatan tingkat program studi (data contoh). */
    private function prodi(): array
    {
        $daftar = [
            ['Workshop Penyusunan Kurikulum OBE', 'Pelatihan', 'Kaprodi TRK'],
            ['Rapat Kerja Program Studi', 'Seminar', 'Kaprodi TRK'],
            ['Studi Banding Prodi ke Politeknik Mitra', 'Kunjungan Industri', 'Sekretaris Prodi'],
            ['Pelatihan Penjaminan Mutu Internal', 'Pelatihan', 'Gugus Penjaminan Mutu'],
            ['Kunjungan Industri Mitra Prodi', 'Kunjungan Industri', 'Koordinator Kerja Sama'],
            ['Seminar Pemetaan Kompetensi Lulusan', 'Seminar', 'Kaprodi TRK'],
        ];
        $status = ['Diajukan', 'Direvisi', 'Disetujui', 'Ditolak'];
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus'];

        $rows = [];
        foreach ($daftar as $i => $d) {
            $rows[] = [
                'id' => (string) ($i + 1),
                'nama' => $d[0],
                'kategori' => $d[1],
                'pengaju' => $d[2],
                'tanggal' => (($i * 4) % 27 + 1).' '.$bulan[$i % 8].' 2026',
                'status' => $status[$i % 4],
                'semester' => $i % 2 === 0 ? 'Ganjil' : 'Genap',
                'kelas' => '',
            ];
        }

        return $rows;
    }
}