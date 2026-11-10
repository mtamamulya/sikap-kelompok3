<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKegiatan;
use App\Models\BuktiPrestasi;
use App\Models\DetailKegiatan;
use App\Models\IsianKategori;
use App\Models\JenisDokumen;
use App\Models\JenisKegiatan;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Prestasi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PrestasiController extends Controller
{
    private const KEGIATAN_CONTOH = [
        'contoh:kmipn' => [
            'label' => 'KMIPN VIII 2026 · Lomba (contoh)',
            'nama' => 'KMIPN VIII 2026',
            'penyelenggara' => 'Kemendikbudristek',
            'mulai' => '2026-09-12',
            'selesai' => '2026-09-14',
        ],
        'contoh:seminar-ai' => [
            'label' => 'Seminar nasional AI · Seminar (contoh)',
            'nama' => 'Seminar nasional AI',
            'penyelenggara' => 'Politeknik Negeri Semarang',
            'mulai' => '2026-09-12',
            'selesai' => '2026-09-12',
        ],
        'contoh:lomba-karya-ilmiah' => [
            'label' => 'Lomba karya ilmiah · Lomba (contoh)',
            'nama' => 'Lomba karya ilmiah',
            'penyelenggara' => 'Universitas Diponegoro',
            'mulai' => '2026-07-15',
            'selesai' => '2026-07-16',
        ],
    ];

    // Halaman daftar prestasi: /prestasi (tujuan tombol "Muat lebih banyak" di landing)
    public function index(Request $request)
    {
        // TODO-DB: ganti dengan Prestasi::orderBy('juara')->paginate(8)
        $semua = collect(config('prestasi', []))->values();

        $perHalaman = 8;
        $halaman = LengthAwarePaginator::resolveCurrentPage();

        $prestasi = new LengthAwarePaginator(
            $semua->forPage($halaman, $perHalaman)->values(),
            $semua->count(),
            $perHalaman,
            $halaman,
            ['path' => $request->url()]
        );

        return view('prestasi-index', compact('prestasi'));
    }

    public function dosenIndex(Request $request)
    {
        $user = $request->user();
        $prestasi = [];

        if ((int) $user?->id_role === 3) {
            $items = Prestasi::query()
                ->with('kegiatan')
                ->whereHas('kegiatan', fn ($query) => $query->where('id_user', $user->getKey()))
                ->latest('id_prestasi')
                ->get();

            $detailByActivity = DB::table('detail_kegiatan')
                ->join('isian_kategori', 'detail_kegiatan.id_isian_kategori', '=', 'isian_kategori.id_isian_kategori')
                ->whereIn('detail_kegiatan.id_kegiatan', $items->pluck('id_kegiatan'))
                ->whereIn('isian_kategori.input', ['penyelenggara', 'tingkat'])
                ->get(['detail_kegiatan.id_kegiatan', 'isian_kategori.input', 'detail_kegiatan.isian'])
                ->groupBy('id_kegiatan')
                ->map(fn ($details) => $details->pluck('isian', 'input'));

            $prestasi = $items->map(function (Prestasi $item) use ($detailByActivity) {
                $details = $detailByActivity->get($item->id_kegiatan, collect());

                return [
                    'id' => $item->getKey(),
                    'nama' => $item->kegiatan->nama_kegiatan,
                    'penghargaan' => $item->capaian ?? $item->catatan ?? '—',
                    'tingkat' => $details->get('tingkat', '—'),
                    'penyelenggara' => $details->get('penyelenggara', '—'),
                ];
            })->all();
        }

        $daftarTingkat = ['Internal', 'Kabupaten/kota', 'Provinsi', 'Nasional', 'Internasional'];
        $selectClass = 'h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-9 pl-3.5 text-[15px] text-gray-900';

        return view('dosen.prestasi.index', compact('prestasi', 'daftarTingkat', 'selectClass'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $isDosen = (int) $user?->id_role === 3;
        $pengaju = $isDosen
            ? [
                'id_user' => (int) $user->getKey(),
                'nama' => $user->dosen?->nama ?? $user->email,
                'status' => 'Dosen',
            ]
            : null;

        $kegiatanLama = $pengaju
            ? Kegiatan::query()
                ->where(function ($query) use ($user) {
                    $query->where('id_user', $user->getKey())
                        ->orWhereExists(function ($membership) use ($user) {
                            $membership->selectRaw('1')
                                ->from('anggota_kegiatan')
                                ->whereColumn('anggota_kegiatan.id_kegiatan', 'kegiatan.id_kegiatan')
                                ->where('anggota_kegiatan.id_user', $user->getKey());
                        });
                })
                ->orderByDesc('id_kegiatan')
                ->get()
                ->mapWithKeys(function (Kegiatan $kegiatan) {
                    $penyelenggara = DB::table('detail_kegiatan')
                        ->join('isian_kategori', 'detail_kegiatan.id_isian_kategori', '=', 'isian_kategori.id_isian_kategori')
                        ->where('detail_kegiatan.id_kegiatan', $kegiatan->getKey())
                        ->whereRaw('LOWER(isian_kategori.input) = ?', ['penyelenggara'])
                        ->value('detail_kegiatan.isian');

                    return [
                        $kegiatan->getKey() => [
                            'label' => $kegiatan->nama_kegiatan,
                            'nama' => $kegiatan->nama_kegiatan,
                            'penyelenggara' => $penyelenggara ?? '',
                            'mulai' => $kegiatan->tanggal_mulai ? substr((string) $kegiatan->tanggal_mulai, 0, 10) : '',
                            'selesai' => $kegiatan->tanggal_selesai ? substr((string) $kegiatan->tanggal_selesai, 0, 10) : '',
                        ],
                    ];
                })
                ->all()
            : [];

        $kandidat = $user?->dosen
            ? User::query()
                ->where(function ($query) {
                    $query->whereHas('dosen')->orWhereHas('mahasiswa');
                })
                ->with(['dosen:id_dosen,id_user,nama', 'mahasiswa:id_mahasiswa,id_user,nama'])
                ->get()
                ->mapWithKeys(fn (User $candidate) => [
                    $candidate->getKey() => [
                        'nama' => $candidate->dosen?->nama ?? $candidate->mahasiswa?->nama,
                        'status' => $candidate->dosen ? 'Dosen' : 'Mahasiswa',
                    ],
                ])
                ->all()
            : [];

        if ($kegiatanLama === []) {
            $kegiatanLama = self::KEGIATAN_CONTOH;
        }

        return view('dosen.prestasi.create', compact('kegiatanLama', 'kandidat', 'pengaju'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if((int) $user?->id_role !== 3, 403, 'Hanya dosen yang dapat mencatat prestasi.');

        $validated = $request->validate([
            'sumber' => ['required', Rule::in(['kegiatan', 'mandiri'])],
            'kegiatan_sebelumnya' => [
                'required_if:sumber,kegiatan',
                'nullable',
            ],
            'nama_prestasi' => ['required', 'string', 'max:255'],
            'penyelenggara' => ['required', 'string', 'max:150'],
            'tingkat' => ['required', Rule::in(['Internal', 'Kabupaten/kota', 'Provinsi', 'Nasional', 'Internasional'])],
            'jenis' => ['required', Rule::in(['Akademik', 'Nonakademik'])],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'capaian' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:150'],
            'bentuk' => ['required', Rule::in(['Individu', 'Tim'])],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'anggota' => ['exclude_unless:bentuk,Tim', 'required', 'array', 'min:2'],
            'anggota.*.id_user' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('users', 'id_user'),
            ],
            'anggota.*.peran' => ['required', Rule::in(['Ketua', 'Anggota', 'Pembimbing'])],
            'dokumen' => ['required', 'array', 'min:1'],
            'dokumen.*.jenis' => ['required', 'string', Rule::in(['Sertifikat', 'Piagam', 'Surat Tugas', 'Dokumentasi', 'Lainnya'])],
            'dokumen.*.tautan' => ['required', 'url', 'max:500'],
        ], [
            'kegiatan_sebelumnya.required_if' => 'Pilih kegiatan sebelumnya.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'anggota.min' => 'Tambahkan minimal satu anggota selain ketua.',
            'dokumen.required' => 'Tambahkan minimal satu dokumen pendukung.',
            'dokumen.min' => 'Tambahkan minimal satu dokumen pendukung.',
            'dokumen.*.tautan.url' => 'Tautan dokumen harus berupa URL yang valid.',
        ]);

        if ($validated['sumber'] === 'kegiatan') {
            $isContoh = array_key_exists($validated['kegiatan_sebelumnya'], self::KEGIATAN_CONTOH);
            $punyaAkses = $isContoh || (ctype_digit((string) $validated['kegiatan_sebelumnya'])
                && Kegiatan::query()
                    ->whereKey($validated['kegiatan_sebelumnya'])
                    ->where(function ($query) use ($user) {
                        $query->where('id_user', $user->getKey())
                            ->orWhereExists(function ($membership) use ($user) {
                                $membership->selectRaw('1')
                                    ->from('anggota_kegiatan')
                                    ->whereColumn('anggota_kegiatan.id_kegiatan', 'kegiatan.id_kegiatan')
                                    ->where('anggota_kegiatan.id_user', $user->getKey());
                            });
                    })
                    ->exists());

            if (! $punyaAkses) {
                throw ValidationException::withMessages([
                    'kegiatan_sebelumnya' => 'Pilih kegiatan yang pernah Anda ajukan atau ikuti.',
                ]);
            }
        }

        if ($validated['bentuk'] === 'Tim') {
            $memberIds = collect($validated['anggota'])->pluck('id_user');
            $otherMemberIds = $memberIds->reject(
                fn ($id) => (int) $id === (int) $user->getKey()
            );
            $candidateCount = User::query()
                ->whereIn('id_user', $otherMemberIds)
                ->where(fn ($query) => $query->whereHas('dosen')->orWhereHas('mahasiswa'))
                ->count();
            $ketua = collect($validated['anggota'])
                ->where('id_user', $user->getKey());
            $invalidPeran = collect($validated['anggota'])
                ->contains(fn ($member) => (int) $member['id_user'] === (int) $user->getKey()
                    ? $member['peran'] !== 'Ketua'
                    : $member['peran'] === 'Ketua');

            if ($candidateCount !== $otherMemberIds->count() || $ketua->count() !== 1 || $invalidPeran) {
                throw ValidationException::withMessages([
                    'anggota' => 'Ketua harus akun dosen yang sedang masuk dan anggota lain harus dosen atau mahasiswa terdaftar.',
                ]);
            }
        }

        DB::transaction(function () use ($validated, $user): void {
            $kategori = $this->kategoriPrestasi();

            $kegiatan = Kegiatan::create([
                'id_user' => $user->getKey(),
                'id_kategori' => $kategori->getKey(),
                'id_kegiatan_sebelumnya' => $validated['sumber'] === 'kegiatan'
                    && ctype_digit((string) $validated['kegiatan_sebelumnya'])
                    ? $validated['kegiatan_sebelumnya']
                    : null,
                'nama_kegiatan' => $validated['nama_prestasi'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
            ]);

            $prestasi = Prestasi::create([
                'id_kegiatan' => $kegiatan->getKey(),
                'jenis_prestasi' => $validated['jenis'] === 'Akademik' ? 'akademik' : 'non-akademik',
                'bidang_lomba' => $validated['kategori'] ?? null,
                'catatan' => $validated['capaian'],
                'capaian' => $validated['capaian'],
            ]);

            foreach ([
                'penyelenggara' => $validated['penyelenggara'],
                'tingkat' => $validated['tingkat'],
                'bentuk_partisipasi' => $validated['bentuk'],
            ] as $input => $value) {
                $field = IsianKategori::firstOrCreate(
                    ['id_kategori' => $kategori->getKey(), 'input' => $input],
                    ['jenis_input' => 'teks', 'aturan' => 'NOT NULL', 'jumlah_isian' => 1]
                );

                DetailKegiatan::create([
                    'id_kegiatan' => $kegiatan->getKey(),
                    'id_isian_kategori' => $field->getKey(),
                    'isian' => $value,
                ]);
            }

            if ($validated['bentuk'] === 'Tim') {
                AnggotaKegiatan::create([
                    'id_kegiatan' => $kegiatan->getKey(),
                    'id_user' => $user->getKey(),
                    'peran' => 'Ketua',
                ]);

                foreach ($validated['anggota'] as $member) {
                    if ((int) $member['id_user'] === (int) $user->getKey()) {
                        continue;
                    }

                    AnggotaKegiatan::create([
                        'id_kegiatan' => $kegiatan->getKey(),
                        'id_user' => $member['id_user'],
                        'peran' => $member['peran'],
                    ]);
                }
            }

            foreach ($validated['dokumen'] as $document) {
                $jenisDokumen = JenisDokumen::firstOrCreate([
                    'nama_jenis_dokumen' => $document['jenis'],
                ]);

                BuktiPrestasi::create([
                    'id_prestasi' => $prestasi->getKey(),
                    'id_jenis_dokumen' => $jenisDokumen->getKey(),
                    'link' => $document['tautan'],
                ]);
            }
        });

        return redirect()->route('prestasi.index')->with('sukses', 'Prestasi berhasil disimpan.');
    }

    private function kategoriPrestasi(): Kategori
    {
        $jenis = JenisKegiatan::firstOrCreate([
            'nama_jenis_kegiatan' => 'Kegiatan Dosen',
        ]);

        return Kategori::firstOrCreate([
            'id_jenis_kegiatan' => $jenis->getKey(),
            'nama_kategori' => 'Prestasi',
        ]);
    }
}
