<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

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
            'q' => trim((string) $request->query('q')),
            'semester' => (string) $request->query('semester'),
            'tingkat' => (string) $request->query('tingkat'),
            'tahun' => (string) $request->query('tahun'),
        ];

        $rows = $semua->filter(function ($r) use ($filter) {
            return ($filter['q'] === '' || mb_stripos($r['mk'].' '.$r['dosen'], $filter['q']) !== false)
                && ($filter['semester'] === '' || $r['semester'] === $filter['semester'])
                && ($filter['tingkat'] === '' || (string) $r['tingkat'] === $filter['tingkat'])
                && ($filter['tahun'] === '' || $r['tahun'] === $filter['tahun']);
        })->values();

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $mataKuliah = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('mahasiswa.dashboard-mhs', [
            'nama' => config('mahasiswa.nama'),
            'ringkasan' => [
                'tahun_akademik' => '2026/2027',
                'jenis_semester' => 'Ganjil',
                'sks_tempuh' => 84,
                'sks_total' => 144,
                'semester_ke' => 5,
                'jumlah_mk' => 7,
                'sks_semester' => 21,
                'pengajuan_aktif' => 2,
                'dokumen_siap' => 1,
            ],
            'tahunList' => ['2026/2027' => '2026/2027', '2025/2026' => '2025/2026', '2024/2025' => '2024/2025'],
            'filter' => $filter,
            'mataKuliah' => $mataKuliah,
        ]);
    }

    public function mataKuliah(Request $request, string $tab = 'semua')
    {
        abort_unless(in_array($tab, ['semua', 'berjalan', 'riwayat'], true), 404);

        $arsip = collect([
            ['Kecerdasan Buatan',  'Angga Wahyu Wibowo', 2, 'Ganjil', '2026/2027'],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil', '2026/2027'],
            ['Sistem Basis Data',  'Wiktasari',          3, 'Genap',  '2025/2026'],
            ['Internet of Things', 'Tahan Prahara',      1, 'Genap',  '2025/2026'],
            ['Computer Vision',    'Prayitno',           2, 'Ganjil', '2026/2027'],
            ['Sistem Operasi',     'Arif Setyawan',      2, 'Ganjil', '2026/2027'],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil', '2025/2026'],
            ['Sistem Basis Data',  'Wiktasari',          3, 'Genap',  '2025/2026'],
            ['Sistem Operasi',     'Arif Setyawan',      1, 'Genap',  '2024/2025'],
            ['Internet of Things', 'Tahan Prahara',      2, 'Ganjil', '2026/2027'],
        ])->map(fn ($r) => ['mk' => $r[0], 'dosen' => $r[1], 'tingkat' => $r[2], 'periode' => $r[3], 'tahun' => $r[4], 'sks' => null]);

        $berjalan = collect([
            ['Kecerdasan Buatan',  'Angga Wahyu Wibowo', 2, 'Ganjil', 2],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil', 4],
            ['Computer Vision',    'Prayitno',           2, 'Ganjil', 3],
            ['Sistem Operasi',     'Arif Setyawan',      2, 'Ganjil', 4],
            ['Jaringan Komputer',  'Kuwat Santoso',      2, 'Ganjil', 4],
            ['Internet of Things', 'Tahan Prahara',      2, 'Ganjil', 4],
        ])->map(fn ($r) => ['mk' => $r[0], 'dosen' => $r[1], 'tingkat' => $r[2], 'periode' => $r[3], 'tahun' => '2026/2027', 'sks' => $r[4]]);

        $filter = [
            'q' => trim((string) $request->query('q')),
            'semester' => (string) $request->query('semester'),
            'tingkat' => (string) $request->query('tingkat'),
            'tahun' => (string) $request->query('tahun'),
        ];

        $rows = $tab === 'berjalan'
            ? $berjalan
            : $arsip->filter(function ($r) use ($filter) {
                return ($filter['q'] === '' || mb_stripos($r['mk'].' '.$r['dosen'], $filter['q']) !== false)
                    && ($filter['semester'] === '' || $r['periode'] === $filter['semester'])
                    && ($filter['tingkat'] === '' || (string) $r['tingkat'] === $filter['tingkat'])
                    && ($filter['tahun'] === '' || $r['tahun'] === $filter['tahun']);
            })->values();

        return view('mahasiswa.mata-kuliah', [
            'nama' => config('mahasiswa.nama'),
            'profil' => ['nim' => config('mahasiswa.nim'), 'prodi' => config('mahasiswa.prodi')],
            'tab' => $tab,
            'rows' => $rows,
            'filter' => $filter,
            'tahunList' => $arsip->pluck('tahun')->unique()->sortDesc()->values()->all(),
            'ringkasan' => [
                'tahun_akademik' => '2026/2027 Ganjil',
                'semester_ke' => 7,
                'sks_tempuh' => 84,
                'sks_total' => 144,
                'sks_diambil' => $berjalan->sum('sks'),
                'jumlah_berjalan' => $berjalan->count(),
            ],
        ]);
    }

    public function prestasi(Request $request)
    {
        $semua = collect([
            [
                'nama' => 'Lomba Inovasi Internet of Things', 'bidang' => 'Teknologi', 'bentuk' => 'Tim',
                'capaian' => 'Juara 2', 'tingkat' => 'Nasional', 'jenis' => 'Akademik',
                'penyelenggara' => 'Forum Inovasi Teknologi', 'tanggal' => '2026-09-24',
                'kegiatan' => 'Lomba inovasi IoT 2026', 'bukti' => null,
            ],
            [
                'nama' => 'Kompetisi Desain Poster Digital', 'bidang' => 'Desain', 'bentuk' => 'Individu',
                'capaian' => 'Juara 1', 'tingkat' => 'Internal', 'jenis' => 'Nonakademik',
                'penyelenggara' => 'BEM Politeknik', 'tanggal' => '2026-08-18',
                'kegiatan' => null, 'bukti' => null,
            ],
        ])->map(fn ($r) => $r + ['tahun' => substr($r['tanggal'], 0, 4)]);

        $filter = [
            'q' => trim((string) $request->query('q')),
            'tingkat' => (string) $request->query('tingkat'),
            'jenis' => (string) $request->query('jenis'),
            'tahun' => (string) $request->query('tahun'),
        ];

        $rows = $semua->filter(function ($r) use ($filter) {
            $teks = $r['nama'].' '.$r['penyelenggara'].' '.($r['kegiatan'] ?? '');

            return ($filter['q'] === '' || mb_stripos($teks, $filter['q']) !== false)
                && ($filter['tingkat'] === '' || $r['tingkat'] === $filter['tingkat'])
                && ($filter['jenis'] === '' || $r['jenis'] === $filter['jenis'])
                && ($filter['tahun'] === '' || $r['tahun'] === $filter['tahun']);
        })->values();

        return view('mahasiswa.prestasi', [
            'nama' => config('mahasiswa.nama'),
            'profil' => ['nim' => config('mahasiswa.nim'), 'prodi' => config('mahasiswa.prodi')],
            'rows' => $rows,
            'total' => $semua->count(),
            'filter' => $filter,
            'tingkatList' => ['Internal', 'Regional', 'Nasional', 'Internasional'],
            'jenisList' => ['Akademik', 'Nonakademik'],
            'tahunList' => $semua->pluck('tahun')->unique()->sortDesc()->values()->all(),
            'ringkasan' => [
                'total' => $semua->count(),
                'akademik' => $semua->where('jenis', 'Akademik')->count(),
                'nonakademik' => $semua->where('jenis', 'Nonakademik')->count(),
                'akademik_ket' => 'Teknologi rekayasa komputer',
                'nonakademik_ket' => 'Desain dan kreativitas',
            ],
        ]);
    }

    public function prestasiTambah()
    {
        return view('mahasiswa.prestasi-tambah', [
            'nama' => config('mahasiswa.nama'),
            'profil' => ['nim' => config('mahasiswa.nim'), 'prodi' => config('mahasiswa.prodi')],
            'tingkatList' => ['Internal', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'],
            'capaianList' => ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Harapan 2', 'Harapan 3', 'Finalis', 'Peserta'],
            'jenisList' => ['Akademik', 'Nonakademik'],
            'jenisDokList' => ['Sertifikat', 'Piagam penghargaan', 'Surat tugas', 'Surat keterangan penyelenggara', 'Foto kegiatan', 'Lainnya'],
            'daftarMahasiswa' => ['Ivan Sigit Santoso', 'Anisa Aulia Nurusyifa', config('mahasiswa.nama'), 'Dewi Lestari', 'Fajar Nugroho', 'Rizky Pratama', 'Salsabila Putri'],
            'peranList' => ['Ketua', 'Anggota'],
            'kegiatanList' => [
                ['label' => 'KMIPN VIII 2026 • Lomba',        'nama' => 'KMIPN VIII 2026',        'penyelenggara' => 'Puspresnas'],
                ['label' => 'Lomba inovasi IoT 2026 • Lomba', 'nama' => 'Lomba Inovasi IoT 2026', 'penyelenggara' => 'Forum Inovasi Teknologi'],
                ['label' => 'Gemastik 2025 • Lomba',          'nama' => 'Gemastik 2025',          'penyelenggara' => 'Puspresnas'],
            ],
        ]);
    }

    public function prestasiSimpan(Request $request)
    {
        $data = $this->validasiPrestasi($request);
        return redirect()->route('mahasiswa.prestasi')->with('status', 'Prestasi berhasil disimpan.');
    }

    public function prestasiEdit(string $prestasi)
    {
        $data = [
            'id' => $prestasi,
            'sumber' => 'sebelumnya',
            'kegiatan' => 'Lomba inovasi IoT 2026 • Lomba',
            'nama_lomba' => 'Lomba Inovasi Internet of Things',
            'penyelenggara' => 'Forum Inovasi Teknologi',
            'lokasi' => 'Semarang',
            'dosen' => 'Tahan Prahara',
            'tingkat' => 'Nasional',
            'capaian' => 'Juara 2',
            'jenis' => 'Akademik',
            'kategori' => 'Teknologi',
            'tanggal_mulai' => '2026-09-22',
            'tanggal_selesai' => '2026-09-24',
            'bentuk' => 'Tim',
            'deskripsi' => 'Mengembangkan sistem pemantauan berbasis IoT untuk lomba tingkat nasional.',
            'anggota' => [
                ['nama' => 'Ivan Sigit Santoso',    'peran' => 'Anggota'],
                ['nama' => 'Anisa Aulia Nurusyifa', 'peran' => 'Anggota'],
            ],
            'dokumen' => [
                ['id' => 11, 'jenis' => 'Sertifikat',    'nama' => 'sertifikat-iot.pdf', 'ukuran' => '1,2 MB', 'url' => null],
                ['id' => 12, 'jenis' => 'Foto kegiatan', 'nama' => 'foto-iot.jpg',       'ukuran' => '2,4 MB', 'url' => null],
            ],
        ];

        return $this->prestasiTambah()->with('prestasi', $data);
    }

    public function prestasiUpdate(Request $request, string $prestasi)
    {
        $data = $this->validasiPrestasi($request);
        return redirect()->route('mahasiswa.prestasi')->with('status', 'Prestasi berhasil diperbarui.');
    }

    public function prestasiHapus(string $prestasi)
    {
        return redirect()->route('mahasiswa.prestasi')->with('status', 'Prestasi berhasil dihapus.');
    }

    private function validasiPrestasi(Request $request): array
    {
        $tingkat = ['Internal', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'];
        $capaian = ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Harapan 2', 'Harapan 3', 'Finalis', 'Peserta'];
        $jenisDok = ['Sertifikat', 'Piagam penghargaan', 'Surat tugas', 'Surat keterangan penyelenggara', 'Foto kegiatan', 'Lainnya'];

        return $request->validate([
            'sumber' => ['required', Rule::in(['sebelumnya', 'mandiri'])],
            'kegiatan' => ['required_if:sumber,sebelumnya', 'nullable', 'string', 'max:150'],
            'nama_lomba' => ['required', 'string', 'max:150'],
            'penyelenggara' => ['required', 'string', 'max:150'],
            'lokasi' => ['required', 'string', 'max:150'],
            'dosen' => ['nullable', 'string', 'max:100'],
            'tingkat' => ['required', Rule::in($tingkat)],
            'capaian' => ['required', Rule::in($capaian)],
            'jenis' => ['required', Rule::in(['Akademik', 'Nonakademik'])],
            'kategori' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'bentuk' => ['required', Rule::in(['Individu', 'Tim'])],
            'anggota' => ['exclude_unless:bentuk,Tim', 'required', 'array', 'min:1'],
            'anggota.*.nama' => ['required', 'string', 'max:150'],
            'anggota.*.peran' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'dokumen' => ['nullable', 'array', 'max:5'],
            'dokumen.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt', 'max:5120'],
            'jenis_dokumen' => ['nullable', 'array', 'max:5'],
            'jenis_dokumen.*' => ['nullable', Rule::in($jenisDok)],
            'dokumen_hapus' => ['nullable', 'array'],
            'dokumen_hapus.*' => ['integer'],
        ], [
            'required' => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi.',
            'in' => ':attribute tidak valid.',
            'max' => ':attribute terlalu panjang atau terlalu besar.',
            'mimes' => 'Format :attribute tidak didukung.',
            'after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'anggota.required' => 'Tambahkan minimal satu anggota tim.',
            'anggota.min' => 'Tambahkan minimal satu anggota tim.',
        ], [
            'nama_lomba' => 'Nama lomba', 'penyelenggara' => 'Penyelenggara', 'lokasi' => 'Lokasi',
            'tingkat' => 'Tingkat kejuaraan', 'capaian' => 'Capaian peringkat', 'jenis' => 'Jenis prestasi',
            'tanggal_mulai' => 'Tanggal mulai', 'tanggal_selesai' => 'Tanggal selesai',
            'bentuk' => 'Bentuk partisipasi', 'kegiatan' => 'Kegiatan sebelumnya',
            'anggota.*.nama' => 'Nama anggota', 'anggota.*.peran' => 'Peran anggota',
            'dokumen.*' => 'File', 'jenis_dokumen.*' => 'Jenis dokumen',
        ]);
    }
}