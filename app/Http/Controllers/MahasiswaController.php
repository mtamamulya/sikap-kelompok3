<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MahasiswaController extends Controller
{
    public function dashboard(Request $request)
    {
        // DATA DUMMY: ganti dengan query Eloquent / API Kelompok 1
        $base = collect([
            ['mk' => 'Kecerdasan Buatan',  'dosen' => 'Angga Wahyu Wibowo', 'tingkat' => 2, 'semester' => 'Ganjil'],
            ['mk' => 'Jaringan Komputer',  'dosen' => 'Kuwat Santoso',      'tingkat' => 2, 'semester' => 'Ganjil'],
            ['mk' => 'Sistem Basis Data',  'dosen' => 'Wiktasari',          'tingkat' => 3, 'semester' => 'Genap'],
            ['mk' => 'Internet of Things', 'dosen' => 'Tahan Prahara',      'tingkat' => 1, 'semester' => 'Genap'],
            ['mk' => 'Computer Vision',    'dosen' => 'Prayitno',           'tingkat' => 2, 'semester' => 'Ganjil'],
            ['mk' => 'Sistem Operasi',     'dosen' => 'Arif Setyawan',      'tingkat' => 2, 'semester' => 'Ganjil'],
        ]);

        $semua = collect(range(1, 5))
            ->flatMap(fn () => $base)
            ->map(fn ($r) => $r + ['tahun' => '2026/2027'])
            ->values();

        $filter = [
            'q'        => trim((string) $request->query('q')),
            'semester' => (string) $request->query('semester'),
            'tingkat'  => (string) $request->query('tingkat'),
            'tahun'    => (string) $request->query('tahun'),
        ];

        $rows = $semua->filter(function ($r) use ($filter) {
            return ($filter['q'] === '' || mb_stripos($r['mk'] . ' ' . $r['dosen'], $filter['q']) !== false)
                && ($filter['semester'] === '' || $r['semester'] === $filter['semester'])
                && ($filter['tingkat']  === '' || (string) $r['tingkat'] === $filter['tingkat'])
                && ($filter['tahun']    === '' || $r['tahun'] === $filter['tahun']);
        })->values();

        $perPage = 10;
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $mataKuliah = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('mahasiswa.dashboard', [
            'nama'      => 'Arya Bima',
            'ringkasan' => [
                'tahun_akademik'  => '2026/2027',
                'jenis_semester'  => 'Ganjil',
                'sks_tempuh'      => 84,
                'sks_total'       => 144,
                'semester_ke'     => 5,
                'jumlah_mk'       => 7,
                'sks_semester'    => 21,
                'pengajuan_aktif' => 2,
                'dokumen_siap'    => 1,
            ],
            'tahunList'  => ['2026/2027' => '2026/2027', '2025/2026' => '2025/2026', '2024/2025' => '2024/2025'],
            'filter'     => $filter,
            'mataKuliah' => $mataKuliah,
        ]);
    }

    public function mataKuliah(Request $request, string $tab = 'semua')
    {
        abort_unless(in_array($tab, ['semua', 'berjalan', 'riwayat'], true), 404);

        // DATA DUMMY: ganti dengan query Eloquent / API Kelompok 1
        // Format: [mata kuliah, dosen, tingkat, periode]
        $arsip = collect([
            ['Kecerdasan Buatan',  'Angga Wahyu Wibowo', 2, 'Ganjil'],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil'],
            ['Sistem Basis Data',  'Wiktasari',          3, 'Genap'],
            ['Internet of Things', 'Tahan Prahara',      1, 'Genap'],
            ['Computer Vision',    'Prayitno',           2, 'Ganjil'],
            ['Sistem Operasi',     'Arif Setyawan',      2, 'Ganjil'],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil'],
            ['Sistem Basis Data',  'Wiktasari',          3, 'Genap'],
            ['Sistem Operasi',     'Arif Setyawan',      1, 'Genap'],
            ['Internet of Things', 'Tahan Prahara',      2, 'Ganjil'],
        ])->map(fn ($r) => ['mk' => $r[0], 'dosen' => $r[1], 'tingkat' => $r[2], 'periode' => $r[3], 'sks' => null]);

        // Format: [mata kuliah, dosen, tingkat, periode, SKS]
        $berjalan = collect([
            ['Kecerdasan Buatan',  'Angga Wahyu Wibowo', 2, 'Ganjil', 2],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil', 4],
            ['Computer Vision',    'Prayitno',           2, 'Ganjil', 3],
            ['Sistem Operasi',     'Arif Setyawan',      2, 'Ganjil', 4],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil', 4],
            ['Internet of Things', 'Tahan Prahara',      2, 'Ganjil', 4],
        ])->map(fn ($r) => ['mk' => $r[0], 'dosen' => $r[1], 'tingkat' => $r[2], 'periode' => $r[3], 'sks' => $r[4]]);

        $filter = [
            'tingkat'  => (string) $request->query('tingkat'),
            'semester' => (string) $request->query('semester'),
            'tahun'    => (string) $request->query('tahun'),
            'q'        => trim((string) $request->query('q')),
        ];

        $rows = ($tab === 'berjalan' ? $berjalan : $arsip)->filter(function ($r) use ($filter, $tab) {
            $cocokQ = $filter['q'] === ''
                || mb_stripos($r['mk'] . ' ' . $r['dosen'], $filter['q']) !== false;
            $cocokSemester = $filter['semester'] === '' || $r['periode'] === $filter['semester'];

            return match ($tab) {
                'semua'   => $cocokQ && $cocokSemester
                             && ($filter['tingkat'] === '' || (string) $r['tingkat'] === $filter['tingkat']),
                'riwayat' => $cocokQ && $cocokSemester,
                default   => true,
            };
        })->values();

        return view('mahasiswa.mata-kuliah', [
            'nama'      => 'Arya Bima',
            'profil'    => ['nim' => '43325115', 'prodi' => 'Teknologi Rekayasa Komputer'],
            'tab'       => $tab,
            'rows'      => $rows,
            'filter'    => $filter,
            'tahunList' => ['2025/2026', '2024/2025', '2023/2024'],
            'ringkasan' => [
                'tahun_akademik'  => '2026/2027 Ganjil',
                'semester_ke'     => 7,
                'sks_tempuh'      => 84,
                'sks_total'       => 144,
                'sks_diambil'     => $berjalan->sum('sks'),
                'jumlah_berjalan' => $berjalan->count(),
            ],
        ]);
    }
}