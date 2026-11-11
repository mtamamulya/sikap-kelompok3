<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;
use App\Repositories\KegiatanProdiData;
use Illuminate\Http\Request;

/**
 * Halaman "Kegiatan" gabungan: tab Semua | Dosen | Mahasiswa | Prodi.
 * Hanya kegiatan yang SUDAH DISETUJUI (pengajuan baru/revisi/tolak = urusan halaman Pengajuan).
 * Dipakai bersama admin (AdminKegiatanController meng-extend kelas ini) dan kaprodi.
 * Sumber data sama dengan laporan PDF/Excel, jadi angka selalu cocok.
 * TODO-DB (Fase C): ganti sumber data dengan query tabel kegiatan; bentuk baris dijaga tetap.
 */
class KaprodiKegiatanController extends Controller
{
    protected string $tampilan = 'kaprodi.kegiatan';

    public function __construct(protected KegiatanData $data) {}

    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), ['semua', 'dosen', 'mahasiswa', 'prodi'], true) ? $request->query('tab') : 'semua';

        $per = [
            'dosen' => $this->petakan('dosen', $this->data->disetujui($this->data->dosen())),
            'mahasiswa' => $this->petakan('mahasiswa', $this->data->disetujui($this->data->mahasiswa())),
            'prodi' => $this->petakan('prodi', $this->data->disetujui(KegiatanProdiData::semua())),
        ];

        // Tab "Semua" = gabungan ketiganya, terbaru di atas.
        $semua = array_merge($per['dosen'], $per['mahasiswa'], $per['prodi']);
        usort($semua, fn ($a, $b) => [$b['raw_date'], $a['nama']] <=> [$a['raw_date'], $b['nama']]);

        $data = ['semua' => $semua] + $per;

        return view($this->tampilan, [
            'data' => $data,
            'tab' => $tab,
            'tahun' => array_map(fn ($rows) => $this->tahun($rows), $data),
        ]);
    }

    /** Daftar tahun (urut naik) dari baris yang sudah dipetakan. */
    private function tahun(array $rows): array
    {
        $t = array_values(array_unique(array_filter(array_column($rows, 'tahun'))));
        sort($t);

        return array_map('intval', $t);
    }

    /** Samakan nama kolom semua jenis kegiatan untuk satu tabel. */
    private function petakan(string $jenis, array $rows): array
    {
        return array_values(array_map(fn ($r) => [
            'id' => (string) $r['id'],
            'jenis' => $jenis,
            'nama' => $r['nama'] ?? '-',
            'kategori' => $r['kategori'] ?? '-',
            'pengaju' => $r['pengaju'] ?? $r['dosen'] ?? $r['penanggung_jawab'] ?? '-',
            'tanggal' => $r['tanggal'] ?? '-',
            'raw_date' => $r['raw_date'] ?? $r['tanggal_iso'] ?? '',
            'tahun' => $r['tahun'] ?? '',
            'status' => $r['status'] ?? '-',
            'semester' => $r['semester'] ?? '',
            'kelas' => $r['kelas'] ?? '',
        ], $rows));
    }
}
