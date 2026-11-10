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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

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
        $data = $this->validasi($request, true);
        $this->simpan($data, $request);

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

        // Jangan tampilkan pesan "berhasil" bila form lama hanya mengirim
        // atribut tampilan yang belum mempunyai padanan kolom database.
        if (! $request->hasAny([
            'id_kegiatan', 'jenis_prestasi', 'bidang_lomba', 'catatan', 'capaian',
        ]) && ! $request->hasFile('foto')) {
            throw ValidationException::withMessages([
                'id_kegiatan' => 'Form edit belum mengirim kolom prestasi yang didukung database.',
            ]);
        }

        $data = $this->validasi($request, false);
        $this->simpan($data, $request, $item);

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

        $anggota = $kegiatan?->anggota
            ?->map(fn ($a) => $this->namaUser($a->user))
            ->filter()
            ->unique()
            ->values()
            ->all() ?? [];

        return [
            'id' => $item->id_prestasi,
            'id_kegiatan' => $item->id_kegiatan,
            // Jika capaian belum ada di tabel, gunakan nama kegiatan sementara.
            'nama' => $item->getAttribute('capaian') ?: ($kegiatan?->nama_kegiatan ?? '-'),
            'jenis_prestasi' => $item->jenis_prestasi,
            'capaian' => $item->getAttribute('capaian'),
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
        return [
            'kegiatanOptions' => Kegiatan::with([
                    'user.dosen',
                    'user.mahasiswa',
                ])
                ->orderBy('nama_kegiatan')
                ->get(['id_kegiatan', 'id_user', 'nama_kegiatan']),

            'jenisDokumenOptions' => JenisDokumen::orderBy('nama_jenis_dokumen')
                ->get(['id_jenis_dokumen', 'nama_jenis_dokumen']),
            'mendukungCapaian' => Schema::hasColumn('prestasi', 'capaian'),
        ];
    }

    /** Validasi mengikuti kolom fisik, bukan nama input simulasi. */
    private function validasi(Request $request, bool $baru): array
    {
        $rules = [
            'id_kegiatan' => [
                $baru ? 'required' : 'sometimes', 'integer',
                Rule::exists('kegiatan', 'id_kegiatan'),
            ],
            'jenis_prestasi' => [
                $baru ? 'required' : 'sometimes',
                Rule::in(['akademik', 'non-akademik']),
            ],
            'bidang_lomba' => ['sometimes', 'nullable', 'string', 'max:150'],
            'catatan' => ['sometimes', 'nullable', 'string'],
            'capaian' => ['sometimes', 'nullable', 'string'],
            'foto' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'id_jenis_dokumen' => [
                'required_with:foto', 'nullable', 'integer',
                Rule::exists('jenis_dokumen', 'id_jenis_dokumen'),
            ],
        ];

        $data = $request->validate($rules);

        // Migration yang diberikan pengguna belum memiliki kolom capaian.
        if ($request->filled('capaian') && ! Schema::hasColumn('prestasi', 'capaian')) {
            throw ValidationException::withMessages([
                'capaian' => 'Kolom capaian belum ada pada tabel prestasi. Koordinasikan migration tim.',
            ]);
        }

        return $data;
    }

    /** Simpan atribut prestasi dan, jika ada, satu bukti baru. */
    private function simpan(array $data, Request $request, ?Prestasi $item = null): void
    {
        $path = null;

        try {
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('bukti-prestasi', 'public');
                if (! $path) {
                    throw new RuntimeException('Gagal menyimpan berkas bukti prestasi.');
                }
            }

            DB::transaction(function () use ($data, $request, $item, $path) {
                $attributes = Arr::only($data, [
                    'id_kegiatan', 'jenis_prestasi', 'bidang_lomba', 'catatan',
                ]);

                if (array_key_exists('capaian', $data) && Schema::hasColumn('prestasi', 'capaian')) {
                    $attributes['capaian'] = $data['capaian'];
                }

                if ($item) {
                    $item->update($attributes);
                } else {
                    $item = Prestasi::create($attributes);
                }

                if ($path !== null) {
                    $bukti = [
                        'id_jenis_dokumen' => $data['id_jenis_dokumen'],
                        'link' => $path,
                    ];

                    if (Schema::hasColumn('bukti_prestasi', 'nama_file')) {
                        $bukti['nama_file'] = $request->file('foto')->getClientOriginalName();
                    }

                    $item->buktiPrestasi()->create($bukti);
                }
            });
        } catch (Throwable $e) {
            // Jika transaksi database gagal, jangan tinggalkan file yatim.
            if ($path !== null) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }
    }
}
