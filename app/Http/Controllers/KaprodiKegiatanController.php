<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;
use App\Repositories\KegiatanProdiData;
use Illuminate\Http\Request;

/**
 * Halaman "Kegiatan" (kaprodi) gabungan: tab Dosen | Mahasiswa | Prodi.
 * Kaprodi HANYA melihat kegiatan yang sudah disetujui (pengajuan baru/revisi/tolak = urusan Pengajuan).
 * Sumber data sama dengan laporan PDF/Excel (KegiatanData & KegiatanProdiData), jadi angka selalu cocok.
 * TODO-DB (Fase C): ganti sumber data dengan query tabel kegiatan; bentuk baris dijaga tetap.
 */
class KaprodiKegiatanController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), ['dosen', 'mahasiswa', 'prodi'], true) ? $request->query('tab') : 'dosen';

        $data = [
            'dosen' => $this->petakan($this->data->disetujui($this->data->dosen())),
            'mahasiswa' => $this->petakan($this->data->disetujui($this->data->mahasiswa())),
            'prodi' => $this->petakan($this->data->disetujui(KegiatanProdiData::semua())),
        ];

        return view('kaprodi.kegiatan', [
            'data' => $data,
            'tab' => $tab,
            'tahun' => array_map(fn ($rows) => $this->data->tahun($rows), $data),
        ]);
    }

    /** Samakan nama kolom semua jenis kegiatan untuk satu tabel. */
    private function petakan(array $rows): array
    {
        return array_values(array_map(fn ($r) => [
            'id' => (string) $r['id'],
            'nama' => $r['nama'] ?? '-',
            'kategori' => $r['kategori'] ?? '-',
            'pengaju' => $r['pengaju'] ?? $r['dosen'] ?? $r['penanggung_jawab'] ?? '-',
            'tanggal' => $r['tanggal'] ?? '-',
            'raw_date' => $r['raw_date'] ?? $r['tanggal_iso'] ?? '',   // untuk sorting tanggal
            'tahun' => $r['tahun'] ?? '',
            'status' => $r['status'] ?? '-',
            'semester' => $r['semester'] ?? '',
            'kelas' => $r['kelas'] ?? '',
        ], $rows));
    }
}
