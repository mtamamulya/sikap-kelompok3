<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use App\Models\Kegiatan;
use App\Models\Prestasi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPrestasiController extends Controller
{
    /** Relasi yang diperlukan oleh halaman daftar, detail, dan edit. */
    private const RELASI = [
        'kegiatan.kategori',
        'kegiatan.user.dosen',
        'kegiatan.user.mahasiswa',
        'kegiatan.anggota.user.dosen',
        'kegiatan.anggota.user.mahasiswa',
        'buktiPrestasi',
        'buktiPrestasi.jenisDokumen',
    ];

    public function index(): View
    {
        // Tidak lagi membuat 28 data simulasi.
        $rows = Prestasi::with(self::RELASI)
            ->orderByDesc('id_prestasi')
            ->get()
            ->map(fn (Prestasi $item) => $this->formatRow($item))
            ->values()
            ->all();

        return view('admin.prestasi', compact('rows'));
    }

    public function show(int $id): View
    {
        $item = Prestasi::with(self::RELASI)->findOrFail($id);
        $prestasi = $this->formatRow($item);

        return view('admin.prestasi-detail', compact('prestasi'));
    }

    public function create(): View
    {
        // Disediakan untuk form; Blade lama belum menggunakan variabel ini.
        return view('admin.prestasi-tambah', $this->pilihanForm());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $this->simpan($data);

        return redirect()->route('admin.prestasi')
            ->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $item = Prestasi::with(self::RELASI)->findOrFail($id);
        $prestasi = $this->formatRow($item);

        return view('admin.prestasi-edit', array_merge(
            compact('prestasi'),
            $this->pilihanForm()
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = Prestasi::findOrFail($id);

        $data = $this->validasi($request, $item);
        $this->simpan($data, $item);

        return redirect()->route('admin.prestasi')
            ->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = Prestasi::with('buktiPrestasi')->findOrFail($id);

        // Hapus hanya berkas yang dikelola controller ini, bukan link eksternal.
        $berkasLokal = $item->buktiPrestasi
            ->pluck('link')
            ->filter(fn ($path) => is_string($path)
                && str_starts_with($path, 'bukti-prestasi/'))
            ->values()
            ->all();

        DB::transaction(function () use ($item) {
            $item->buktiPrestasi()->delete();
            $item->delete();
            // Kegiatan tetap dipertahankan karena bisa dipakai modul lain.
        });

        if ($berkasLokal !== []) {
            Storage::disk('public')->delete($berkasLokal);
        }

        return redirect()->route('admin.prestasi')
            ->with('success', 'Prestasi berhasil dihapus.');
    }

    /**
     * Ubah data Eloquent menjadi bentuk yang diharapkan DataTables dan
     * Blade detail/edit saat ini. Tidak mengarang data yang tidak ada di ERD.
     */
    private function formatRow(Prestasi $item): array
    {
        $kegiatan = $item->kegiatan;
        $pemilik = $kegiatan?->user;
        $tanggal = $kegiatan?->tanggal_selesai;
        $formData = $item->form_data ?? [];

        $anggota = $kegiatan?->anggota
            ?->map(fn ($a) => $this->namaUser($a->user))
            ->filter()
            ->unique()
            ->values()
            ->all() ?? [];
        $anggotaForm = $formData['anggota'] ?? [];
        if ($anggotaForm === []) {
            $anggotaForm = $kegiatan?->anggota
                ?->map(fn ($a) => [
                    'nama' => $this->namaUser($a->user) ?? '',
                    'peran' => $a->peran ?? 'Anggota',
                ])
                ->values()
                ->all() ?? [];
        }
        $dokumen = $item->buktiPrestasi->map(fn ($b) => [
            'id' => $b->id_bukti_prestasi,
            'jenis' => $b->jenisDokumen?->nama_jenis_dokumen === 'Sertifikat'
                ? 'Sertifikat'
                : 'Dokumentasi kegiatan',
            'nama' => $b->nama_file ?? basename($b->link),
            'ukuran' => '',
            'url' => $b->link,
        ])->all();

        return [
            'id' => $item->id_prestasi,
            'id_kegiatan' => $item->id_kegiatan,
            'sumber' => $formData['sumber'] ?? ($kegiatan ? 'sebelumnya' : 'mandiri'),
            'kegiatan' => $formData['kegiatan'] ?? $kegiatan?->nama_kegiatan ?? '',
            'nama_lomba' => $formData['nama_lomba'] ?? $item->getAttribute('capaian') ?? $kegiatan?->nama_kegiatan ?? '',
            'penyelenggara' => $formData['penyelenggara'] ?? $this->namaUser($pemilik) ?? '',
            'lokasi' => $formData['lokasi'] ?? $kegiatan?->lokasi ?? '',
            'dosen' => $formData['dosen'] ?? '',
            'tingkat' => $formData['tingkat'] ?? 'Internal',
            'capaian' => $formData['capaian'] ?? $item->getAttribute('capaian') ?? '',
            'jenis' => $formData['jenis'] ?? ucfirst(str_replace('-', '', $item->jenis_prestasi)),
            'kategori_form' => $formData['kategori'] ?? $item->bidang_lomba ?? '',
            'tanggal_mulai' => $formData['tanggal_mulai'] ?? $kegiatan?->tanggal_mulai?->toDateString() ?? '',
            'tanggal_selesai' => $formData['tanggal_selesai'] ?? $kegiatan?->tanggal_selesai?->toDateString() ?? '',
            'bentuk' => $formData['bentuk'] ?? ($anggotaForm === [] ? 'Individu' : 'Tim'),
            'anggota_form' => $anggotaForm,
            'deskripsi' => $formData['deskripsi'] ?? $item->catatan ?? '',
            'dokumen' => $dokumen,
            'nama' => ($formData['nama_lomba'] ?? null) ?: ($item->getAttribute('capaian') ?: ($kegiatan?->nama_kegiatan ?? '-')),
            'jenis_prestasi' => $item->jenis_prestasi,
            'bidang_lomba' => $item->bidang_lomba,
            'catatan' => $item->catatan,
            // Kategori kegiatan bukan otomatis "Lomba/Penghargaan".
            'kategori' => $kegiatan?->kategori?->nama_kategori ?? '-',
            // id_user pada kegiatan = pemilik/pengaju, belum tentu pengunggah bukti.
            'pengunggah' => $this->namaUser($pemilik) ?? '-',
            'tipe' => match ((int) ($pemilik?->id_role ?? 0)) {
                3 => 'Dosen',
                4 => 'Mahasiswa',
                default => '-',
            },
            'anggota' => $anggota,
            // Tanggal selesai kegiatan dipakai sementara, bukan tanggal raihan definitif.
            'tanggal' => $tanggal?->locale('id')->translatedFormat('d F Y') ?? '-',
            'raw_date' => $tanggal?->toDateString(),
            // ERD kegiatan/prestasi belum memberi semester prestasi yang pasti.
            'semester' => '-',
            'bukti' => $item->buktiPrestasi->map(fn ($b) => [
                'id' => $b->id_bukti_prestasi,
                'link' => $b->link,
            ])->all(),
            'gambar' => null,
        ];
    }

    private function namaUser(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        return $user->dosen?->nama
            ?? $user->mahasiswa?->nama
            ?? $user->email;
    }

    private function pilihanForm(): array
    {
        $kegiatan = Kegiatan::with([
            'user.dosen',
            'user.mahasiswa',
        ])->orderBy('nama_kegiatan')->get();

        return [
            'kegiatanOptions' => $kegiatan,
            'kegiatanList' => $kegiatan->map(fn (Kegiatan $k) => [
                'id' => $k->id_kegiatan,
                'label' => $k->nama_kegiatan,
                'nama' => $k->nama_kegiatan,
                'penyelenggara' => $this->namaUser($k->user) ?? '',
            ])->all(),
            'tingkatList' => ['Internal', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'],
            'capaianList' => ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Harapan 2', 'Harapan 3', 'Finalis', 'Peserta'],
            'jenisList' => ['Akademik', 'Nonakademik'],
            'jenisDokList' => ['Sertifikat', 'Dokumentasi kegiatan'],
            'daftarMahasiswa' => DB::table('mahasiswa')->orderBy('nama')->pluck('nama')->all(),
            'peranList' => ['Ketua', 'Anggota'],

            'jenisDokumenOptions' => JenisDokumen::orderBy('nama_jenis_dokumen')
                ->get(['id_jenis_dokumen', 'nama_jenis_dokumen']),
        ];
    }

    private function validasi(Request $request, ?Prestasi $item = null): array
    {
        $rules = [
            'sumber' => ['required', Rule::in(['sebelumnya', 'mandiri'])],
            'kegiatan' => ['required_if:sumber,sebelumnya', 'nullable', 'string', 'max:150'],
            'id_kegiatan' => [
                'required_if:sumber,sebelumnya', 'nullable', 'integer',
                Rule::exists('kegiatan', 'id_kegiatan'),
            ],
            'nama_lomba' => ['required', 'string', 'max:150'],
            'penyelenggara' => ['required', 'string', 'max:150'],
            'lokasi' => ['required', 'string', 'max:150'],
            'dosen' => ['nullable', 'string', 'max:100'],
            'tingkat' => ['required', Rule::in(['Internal', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'])],
            'capaian' => ['required', Rule::in(['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Harapan 2', 'Harapan 3', 'Finalis', 'Peserta'])],
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
            'dokumen.*.jenis' => ['nullable', Rule::in(['Sertifikat', 'Dokumentasi kegiatan'])],
            'dokumen_pertahankan' => ['nullable', 'array', 'max:5'],
            'dokumen_pertahankan.*' => ['integer'],
            'dokumen_hapus' => ['nullable', 'array'],
            'dokumen_hapus.*' => ['integer'],
        ];

        $validator = Validator::make($request->all(), $rules, [
            'required' => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi.',
            'in' => ':attribute tidak valid.',
            'max' => ':attribute terlalu panjang atau terlalu banyak.',
            'after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'anggota.required' => 'Tambahkan minimal satu anggota tim.',
            'anggota.min' => 'Tambahkan minimal satu anggota tim.',
        ]);

        $validator->after(function ($validator) use ($request, $item) {
            $sumber = $request->input('sumber');
            $kegiatanId = $request->input('id_kegiatan');
            if ($sumber === 'sebelumnya' && $kegiatanId) {
                $namaKegiatan = Kegiatan::whereKey($kegiatanId)->value('nama_kegiatan');
                if ($namaKegiatan !== $request->input('kegiatan')) {
                    $validator->errors()->add('kegiatan', 'Pilih kegiatan dari daftar yang tersedia.');
                }
            }

            $dokumenList = $request->input('dokumen', []);
            $jumlahDokumenBaru = 0;
            $tautan = [];
            if (is_array($dokumenList)) {
                foreach ($dokumenList as $index => $dokumen) {
                    if (! is_array($dokumen)) {
                        continue;
                    }

                    $punyaTautan = filled($dokumen['url'] ?? null);
                    $punyaJenis = filled($dokumen['jenis'] ?? null);
                    if ($punyaTautan && $punyaJenis) {
                        $jumlahDokumenBaru++;
                        $tautan[] = strtolower($dokumen['url']);
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
            $dokumenDipertahankan = is_array($dokumenDipertahankan) ? $dokumenDipertahankan : [];
            if ($item && $dokumenDipertahankan !== []) {
                $buktiDipertahankan = $item->buktiPrestasi()
                    ->whereIn('id_bukti_prestasi', $dokumenDipertahankan)
                    ->get(['id_bukti_prestasi', 'link']);
                if ($buktiDipertahankan->count() !== count(array_unique($dokumenDipertahankan))) {
                    $validator->errors()->add('dokumen_pertahankan', 'Dokumen yang dipertahankan tidak valid.');
                }
                foreach ($buktiDipertahankan as $bukti) {
                    $tautan[] = strtolower($bukti->link);
                }
            } elseif (! $item && $dokumenDipertahankan !== []) {
                $validator->errors()->add('dokumen_pertahankan', 'Dokumen lama tidak dapat digunakan untuk prestasi baru.');
            }

            if (count($tautan) !== count(array_unique($tautan))) {
                $validator->errors()->add('dokumen', 'Tautan dokumen tidak boleh duplikat.');
            }

            $jumlahDipertahankan = count($dokumenDipertahankan);
            if ($jumlahDokumenBaru + $jumlahDipertahankan === 0) {
                $validator->errors()->add('dokumen', 'Tambahkan minimal satu tautan bukti prestasi.');
            } elseif ($jumlahDokumenBaru + $jumlahDipertahankan > 5) {
                $validator->errors()->add('dokumen', 'Maksimal 5 dokumen.');
            }
        });

        return $validator->validate();
    }

    /** Simpan data formulir dan tautan bukti prestasi. */
    private function simpan(array $data, ?Prestasi $item = null): void
    {
        $formData = Arr::only($data, [
            'sumber', 'kegiatan', 'nama_lomba', 'penyelenggara', 'lokasi',
            'dosen', 'tingkat', 'capaian', 'jenis', 'kategori',
            'tanggal_mulai', 'tanggal_selesai', 'bentuk', 'anggota', 'deskripsi',
        ]);
        $attributes = [
            'id_kegiatan' => $data['sumber'] === 'sebelumnya' ? $data['id_kegiatan'] : null,
            'jenis_prestasi' => $data['jenis'] === 'Akademik' ? 'akademik' : 'non-akademik',
            'bidang_lomba' => $data['kategori'] ?? null,
            'catatan' => $data['deskripsi'] ?? null,
            'capaian' => $data['capaian'],
            'form_data' => $formData,
        ];
        $dokumenDipertahankan = $data['dokumen_pertahankan'] ?? [];

        DB::transaction(function () use ($data, $attributes, $item, $dokumenDipertahankan) {
            if ($item) {
                $item->update($attributes);
                $item->buktiPrestasi()
                    ->whereNotIn('id_bukti_prestasi', $dokumenDipertahankan)
                    ->delete();
            } else {
                $item = Prestasi::create($attributes);
            }

            foreach ($data['dokumen'] ?? [] as $dokumen) {
                if (blank($dokumen['url'] ?? null) || blank($dokumen['jenis'] ?? null)) {
                    continue;
                }

                $jenis = JenisDokumen::firstOrCreate([
                    'nama_jenis_dokumen' => $dokumen['jenis'],
                ]);
                $item->buktiPrestasi()->create([
                    'id_jenis_dokumen' => $jenis->id_jenis_dokumen,
                    'link' => $dokumen['url'],
                ]);
            }
        });
    }
}
