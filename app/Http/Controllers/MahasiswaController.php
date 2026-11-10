<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
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

        return view('mahasiswa.mahasiswa.dashboard-mhs', [
            'nama' => config('mahasiswa.nama'),
            'ringkasan' => [
                'tahun_akademik' => '2026/2027',
                'jenis_semester' => 'Ganjil',
                'sks_tempuh' => 84,
                'sks_total' => 144,
                'semester_ke' => config('mahasiswa.semester'),
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

    public function mataKuliah(Request $request, string $tab = 'berjalan')
    {
        abort_unless(in_array($tab, ['semua', 'berjalan', 'katalog', 'riwayat'], true), 404);
        $tab = $tab === 'riwayat' ? 'semua' : $tab;

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

        $katalog = collect([
            ['Algoritma dan Pemrograman', 'Arif Setyawan', 1, 'Ganjil', 4, '2024/2025'],
            ['Matematika Dasar', 'Prayitno', 1, 'Ganjil', 3, '2024/2025'],
            ['Dasar Elektronika', 'Kuwat Santoso', 1, 'Ganjil', 3, '2024/2025'],
            ['Internet of Things', 'Tahan Prahara', 1, 'Genap', 4, '2024/2025'],
            ['Sistem Operasi', 'Arif Setyawan', 1, 'Genap', 4, '2024/2025'],
            ['Kecerdasan Buatan', 'Angga Wahyu Wibowo', 2, 'Ganjil', 2, '2026/2027'],
            ['Jaringan Komputer', 'Kuwat Santoso', 2, 'Ganjil', 4, '2025/2026'],
            ['Computer Vision', 'Prayitno', 2, 'Ganjil', 3, '2026/2027'],
            ['Pemrograman Web', 'Wiktasari', 2, 'Genap', 4, '2025/2026'],
            ['Mikrokontroler', 'Tahan Prahara', 2, 'Genap', 3, '2025/2026'],
            ['Sistem Basis Data', 'Wiktasari', 3, 'Ganjil', 4, '2025/2026'],
            ['Keamanan Jaringan', 'Kuwat Santoso', 3, 'Ganjil', 3, '2026/2027'],
            ['Machine Learning', 'Angga Wahyu Wibowo', 3, 'Genap', 3, '2026/2027'],
            ['Rekayasa Perangkat Lunak', 'Prayitno', 3, 'Genap', 3, '2025/2026'],
            ['Proyek Akhir', 'Arif Setyawan', 4, 'Ganjil', 6, '2026/2027'],
            ['Manajemen Proyek TI', 'Wiktasari', 4, 'Ganjil', 2, '2026/2027'],
            ['Magang Industri', 'Tahan Prahara', 4, 'Genap', 6, '2026/2027'],
            ['Tugas Akhir', 'Angga Wahyu Wibowo', 4, 'Genap', 6, '2026/2027'],
        ])->map(fn ($r) => [
            'mk' => $r[0], 'dosen' => $r[1], 'tingkat' => $r[2],
            'periode' => $r[3], 'sks' => $r[4], 'tahun' => $r[5],
        ]);

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

        $cocok = fn ($r) => ($filter['q'] === '' || mb_stripos($r['mk'].' '.$r['dosen'], $filter['q']) !== false)
            && ($filter['semester'] === '' || $r['periode'] === $filter['semester'])
            && ($filter['tingkat'] === '' || (string) $r['tingkat'] === $filter['tingkat'])
            && ($filter['tahun'] === '' || $r['tahun'] === $filter['tahun']);

        $rows = match ($tab) {
            'berjalan' => $berjalan,
            'katalog' => $katalog->filter($cocok)->sortBy([['tingkat', 'asc'], ['mk', 'asc']])->values(),
            default => $arsip->filter($cocok)->values(),
        };
        $tahunList = $arsip->pluck('tahun')
            ->merge($katalog->pluck('tahun'))
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return view('mahasiswa.mahasiswa.mata-kuliah', [
            'nama' => config('mahasiswa.nama'),
            'profil' => ['nim' => config('mahasiswa.nim'), 'prodi' => config('mahasiswa.prodi')],
            'tab' => $tab,
            'rows' => $rows,
            'filter' => $filter,
            'tahunList' => $tahunList,
            'ringkasan' => [
                'tahun_akademik' => '2026/2027 Ganjil',
                'semester_ke' => config('mahasiswa.semester'),
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

        return view('mahasiswa.mahasiswa.prestasi', [
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
        $demoActivities = collect([
            ['FKBSE 2025', 'Pengabdian', 'Himpunan Mahasiswa TRK'],
            ['KMIPN VIII 2026', 'Lomba', 'Politeknik Negeri'],
            ['IMAKEN Progress In Action 2026', 'Volunteer', 'IMAKEN'],
            ['Google Singapura', 'Kunjungan Industri', 'Google Asia Pacific'],
            ['KKL Schneider Electric', 'Kuliah Kerja Lapangan', 'Schneider Electric Indonesia'],
            ['Amazon Web Service', 'Magang', 'Amazon Web Services'],
            ['Hackathon Nasional 2025', 'Lomba', 'Kementerian Kominfo'],
            ['Workshop IoT & Embedded System', 'Pelatihan', 'Lab Embedded TRK'],
            ['Studi Independen AI Micro-Credential', 'Magang', 'Kampus Merdeka x AI Center'],
            ['Pengabdian Desa Digital Banyumanik', 'Pengabdian', 'Jurusan Elektro Polines'],
            ['Seminar International TRK Tech', 'Seminar', 'TRK Polines'],
            ['Lomba Karya Tulis Ilmiah 2025', 'Penelitian', 'Universitas Indonesia'],
            ['Lomba CTF Cyber Security 2024', 'Lomba', 'BSSN'],
            ['Pelatihan Cloud Architecture AWS', 'Pelatihan', 'AWS Educate'],
            ['Volunteer Mengajar AI Sekolah Dasar', 'Volunteer', 'Komunitas AI Semarang'],
            ['Bootcamp Fullstack Web Developer', 'Pelatihan', 'Dicoding Indonesia'],
            ['Kunjungan Industri Telkomsel Digital', 'Kunjungan Industri', 'Telkomsel Smart Office'],
            ['Kompetisi Robotics & Automation', 'Lomba', 'ITB Robotics Club'],
        ])->map(fn (array $activity) => [
            'label' => $activity[0].' • '.$activity[1],
            'nama' => $activity[0],
            'penyelenggara' => $activity[2],
        ]);
        $submittedActivities = app(PengajuanController::class)->activityOptions()
            ->map(fn (array $activity) => [
                'label' => ($activity['title'] ?? 'Pengajuan').' • '.($activity['category'] ?? 'Kegiatan'),
                'nama' => $activity['title'] ?? 'Pengajuan',
                'penyelenggara' => $activity['penyelenggara'] ?? '',
            ]);
        return view('mahasiswa.mahasiswa.prestasi-tambah', [
            'nama' => config('mahasiswa.nama'),
            'profil' => ['nim' => config('mahasiswa.nim'), 'prodi' => config('mahasiswa.prodi')],
            'tingkatList' => ['Internal', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'],
            'capaianList' => ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Harapan 2', 'Harapan 3', 'Finalis', 'Peserta'],
            'jenisList' => ['Akademik', 'Nonakademik'],
            'jenisDokList' => ['Sertifikat', 'Dokumentasi kegiatan'],
            'daftarMahasiswa' => ['Ivan Sigit Santoso', 'Anisa Aulia Nurusyifa', config('mahasiswa.nama'), 'Dewi Lestari', 'Fajar Nugroho', 'Rizky Pratama', 'Salsabila Putri'],
            'peranList' => ['Ketua', 'Anggota'],
            'kegiatanList' => $demoActivities->concat($submittedActivities)->unique('label')->values(),
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
                ['id' => 12, 'jenis' => 'Dokumentasi kegiatan', 'nama' => 'foto-iot.jpg', 'ukuran' => '2,4 MB', 'url' => null],
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
        $jenisDok = ['Sertifikat', 'Dokumentasi kegiatan'];

        $validator = Validator::make($request->all(), [
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
            'dokumen.*' => ['array:url,jenis'],
            'dokumen.*.url' => [
                'nullable', 'url', 'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $host = strtolower((string) parse_url($value, PHP_URL_HOST));
                    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
                    $domainDiizinkan = in_array($host, [
                        'drive.google.com',
                        'docs.google.com',
                        '1drv.ms',
                        'onedrive.live.com',
                    ], true) || str_ends_with($host, '.sharepoint.com');

                    if ($scheme !== 'https' || ! $domainDiizinkan) {
                        $fail('Tautan harus berupa tautan berbagi Google Drive atau OneDrive yang aman (https).');
                    }
                },
            ],
            'dokumen.*.jenis' => ['nullable', Rule::in($jenisDok)],
            'dokumen_pertahankan' => ['nullable', 'array', 'max:5'],
            'dokumen_pertahankan.*' => ['integer'],
            'dokumen_hapus' => ['nullable', 'array'],
            'dokumen_hapus.*' => ['integer'],
        ], [
            'required' => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi.',
            'in' => ':attribute tidak valid.',
            'max' => ':attribute terlalu panjang atau terlalu banyak.',
            'dokumen.*.url.url' => 'Masukkan tautan Google Drive atau OneDrive yang valid.',
            'after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'anggota.required' => 'Tambahkan minimal satu anggota tim.',
            'anggota.min' => 'Tambahkan minimal satu anggota tim.',
        ], [
            'nama_lomba' => 'Nama lomba', 'penyelenggara' => 'Penyelenggara', 'lokasi' => 'Lokasi',
            'tingkat' => 'Tingkat kejuaraan', 'capaian' => 'Capaian peringkat', 'jenis' => 'Jenis prestasi',
            'tanggal_mulai' => 'Tanggal mulai', 'tanggal_selesai' => 'Tanggal selesai',
            'bentuk' => 'Bentuk partisipasi', 'kegiatan' => 'Kegiatan sebelumnya',
            'anggota.*.nama' => 'Nama anggota', 'anggota.*.peran' => 'Peran anggota',
            'dokumen.*.url' => 'Tautan bukti', 'dokumen.*.jenis' => 'Jenis dokumen',
        ]);

        $validator->after(function ($validator) use ($request) {
            $dokumenList = $request->input('dokumen', []);
            $jumlahDokumenBaru = 0;
            if (is_array($dokumenList)) {
                foreach ($dokumenList as $index => $dokumen) {
                    if (! is_array($dokumen)) {
                        continue;
                    }

                    $punyaTautan = filled($dokumen['url'] ?? null);
                    $punyaJenis = filled($dokumen['jenis'] ?? null);

                    if ($punyaTautan && $punyaJenis) {
                        $jumlahDokumenBaru++;
                    }

                    if ($punyaTautan !== $punyaJenis) {
                        $field = $punyaTautan ? "dokumen.$index.jenis" : "dokumen.$index.url";
                        $validator->errors()->add($field, $punyaTautan
                            ? 'Pilih jenis dokumen untuk tautan ini.'
                            : 'Masukkan tautan untuk jenis dokumen ini.');
                    }
                }
            }

            $dokumenDipertahankan = $request->input('dokumen_pertahankan', []);
            $jumlahDipertahankan = is_array($dokumenDipertahankan) ? count($dokumenDipertahankan) : 0;
            if ($jumlahDokumenBaru + $jumlahDipertahankan === 0) {
                $validator->errors()->add('dokumen', 'Tambahkan minimal satu tautan bukti prestasi.');
            }
        });

        return $validator->validate();
    }
}
