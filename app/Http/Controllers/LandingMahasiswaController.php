<?php

namespace App\Http\Controllers;

use App\Repositories\MahasiswaStore;
use App\Repositories\PenempatanKelasMahasiswa;
use App\Support\NimMahasiswa;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class LandingMahasiswaController extends Controller
{
    public function __construct(private MahasiswaStore $store) {}

    /** Daftar mahasiswa. */
    public function index()
    {
        $students = array_map(fn ($r) => [
            'id' => $r['id'],
            'nim' => $r['nim'],
            'name' => $r['nama'],
            'level' => $r['level'],
            'className' => $r['kelas'],
            'status' => $r['status'],
        ], $this->store->all());

        return view('admin.mahasiswa', ['students' => $students, 'levels' => MahasiswaStore::TINGKAT]);
    }

    /** Detail satu mahasiswa. */
    public function show(string $id)
    {
        $row = $this->store->find($id);
        abort_unless($row, 404);

        // 1. Ambil data foto dari tabel users secara langsung menggunakan relasi Eloquent
        $mhsModel = \App\Models\Mahasiswa::with('user')->find($id);
        $fotoProfil = $mhsModel && $mhsModel->user ? $mhsModel->user->foto_profil : null;

        // 2. Sisipkan key 'foto' ke dalam array mahasiswa
        $mahasiswa = [
            'id' => $row['id'], 
            'nama' => $row['nama'], 
            'nim' => $row['nim'], 
            'status' => $row['status'],
            'foto' => $fotoProfil
        ];

        $academic = [
            ['Angkatan', $row['angkatan']],
            ['Program studi', config('admin.prodi.nama', 'Teknologi Rekayasa Komputer').', '.config('admin.prodi.jenjang', 'D4')],
            ['Tingkat saat ini', $row['level']],
            ['Kelas saat ini', \Illuminate\Support\Str::after($row['kelas'], 'TI-')],
        ];

        $identity = [
            ['Email akun', $row['email']],
            ['Akun login', $row['status'] === 'Non-aktif' ? 'Nonaktif' : 'Aktif'],
        ];

        // 3. Fallback dummy data ikut dirapikan agar jika database kosong, yang muncul tetap TRK D4 (bukan Teknik Informatika)
        $history = $this->store->riwayatKelas($id) ?? [
            ['year' => '2025/2026', 'current' => true, 'sem' => 'Ganjil', 'prodi' => config('admin.prodi.nama', 'Teknologi Rekayasa Komputer'), 'level' => $row['level'], 'cls' => \Illuminate\Support\Str::after($row['kelas'], 'TI-')],
            ['year' => '2024/2025', 'sem' => 'Genap',  'prodi' => config('admin.prodi.nama', 'Teknologi Rekayasa Komputer'), 'level' => 'Tingkat 1', 'cls' => '1B'],
            ['year' => '2024/2025', 'sem' => 'Ganjil', 'prodi' => config('admin.prodi.nama', 'Teknologi Rekayasa Komputer'), 'level' => 'Tingkat 1', 'cls' => '1B'],
        ];

        $kegiatan = $this->store->kegiatan($id) ?? [
            ['title' => 'Kompetisi Pengembangan Aplikasi Web', 'year' => '2026', 'type' => 'Lomba',    'role' => 'Ketua Tim'],
            ['title' => 'Pelatihan Dasar Jaringan Komputer',   'year' => '2025', 'type' => 'Pelatihan', 'role' => 'Peserta'],
            ['title' => 'Seminar Nasional Teknologi Informasi', 'year' => '2025', 'type' => 'Seminar',  'role' => 'Panitia'],
        ];

        return view('admin.mahasiswa-detail', compact('mahasiswa', 'academic', 'identity', 'history', 'kegiatan'));
    }

    /** Form tambah mahasiswa. */
    public function create()
    {
        return view('admin.mahasiswa-tambah', [
            'tahun' => range((int) date('Y'), (int) date('Y') - 9),
            'statusAkad' => $this->store->opsiStatus(),
        ]);
    }

    /** Simpan mahasiswa baru. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'nim' => $this->aturanNim($request, ''),
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'telepon' => ['nullable', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'angkatan' => ['required', 'integer', 'between:2000,'.((int) date('Y') + 1)],
            'status' => ['required', Rule::in($this->store->opsiStatus())],
            'email' => ['required', 'email', 'max:150', 'ends_with:@mhs.polines.ac.id', function ($attr, $value, $fail) {
                if ($this->store->usedByOther('email', $value, '')) {
                    $fail('Email sudah dipakai akun lain.');
                }
            }],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.regex' => 'NIM harus berupa angka 8-12 digit.',
            'tanggal_lahir.date' => 'Tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'telepon.regex' => 'No. telepon hanya boleh angka, spasi, tanda + dan -.',
            'angkatan.required' => 'Angkatan wajib dipilih.',
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.ends_with' => 'Email harus menggunakan domain institusi @mhs.polines.ac.id.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // TODO-DB: simpan ke database (mahasiswa + akun login dalam satu transaksi).
        $row = $this->store->create($data);

        return redirect()->route('admin.mahasiswa.show', $row['id'])->with('status', 'Mahasiswa baru berhasil ditambahkan.');
    }

    /** Form edit mahasiswa. */
    public function edit(string $id)
    {
        $mahasiswa = $this->store->find($id);
        abort_unless($mahasiswa, 404);

        $tahun = range((int) date('Y'), (int) date('Y') - 9);
        if (! in_array((int) $mahasiswa['angkatan'], $tahun, true)) {
            $tahun[] = (int) $mahasiswa['angkatan'];
            rsort($tahun);
        }

        return view('admin.mahasiswa-edit', [
            'mahasiswa' => $mahasiswa,
            'tahun' => $tahun,
            'statusAkad' => $this->store->opsiStatus(),
            'fotoUrl' => ! empty($mahasiswa['foto']) ? Storage::disk('public')->url($mahasiswa['foto']) : null,
        ]);
    }

    /** Simpan perubahan mahasiswa. */
    public function update(Request $request, string $id): RedirectResponse
    {
        $lama = $this->store->find($id);
        abort_unless($lama, 404);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'nim' => $this->aturanNim($request, $id),
            'angkatan' => ['required', 'integer', 'between:2000,'.((int) date('Y') + 1)],
            'status' => ['required', Rule::in($this->store->opsiStatus())],
            'email' => ['required', 'email', 'max:150', 'ends_with:@mhs.polines.ac.id', function ($attr, $value, $fail) use ($id) {
                if ($this->store->usedByOther('email', $value, $id)) {
                    $fail('Email sudah dipakai akun lain.');
                }
            }],
            'password' => ['nullable', 'string', 'min:8'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.regex' => 'NIM harus berupa angka 8-12 digit.',
            'angkatan.required' => 'Angkatan wajib dipilih.',
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.ends_with' => 'Email harus menggunakan domain institusi @mhs.polines.ac.id.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Foto profil: ganti atau hapus. Tanpa aksi apa pun, foto lama dibiarkan.
        $fotoLama = $lama['foto'] ?? null;
        unset($data['foto']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mahasiswa-foto', 'public');
            if ($fotoLama) {
                Storage::disk('public')->delete($fotoLama);
            }
        } elseif ($request->boolean('hapus_foto')) {
            $data['foto'] = null;
            if ($fotoLama) {
                Storage::disk('public')->delete($fotoLama);
            }
        }

        // TODO-DB: simpan ke database (mahasiswa + akun login dalam satu transaksi).
        $this->store->update($id, $data);

        return redirect()->route('admin.mahasiswa.show', $id)->with('status', 'Perubahan data mahasiswa berhasil disimpan.');
    }

    /** Hapus mahasiswa beserta akun dan riwayat kelasnya. */
    public function destroy(string $id): RedirectResponse
    {
        $hasil = $this->store->delete($id);
        abort_if($hasil === 'tidak-ada', 404);

        if ($hasil === 'dipakai') {
            return redirect()->route('admin.mahasiswa')
                ->with('error', 'Mahasiswa masih dipakai data lain (mis. prestasi), jadi tidak bisa dihapus. Ubah statusnya menjadi Non-aktif.');
        }

        return redirect()->route('admin.mahasiswa')->with('status', 'Mahasiswa berhasil dihapus.');
    }

    /** Tempatkan semua mahasiswa aktif ke kelas pada tahun akademik aktif (hanya mode database). */
    public function buatKelasSemester(PenempatanKelasMahasiswa $penempatan): RedirectResponse
    {
        abort_unless(config('admin.data_source', 'json') === 'database', 404);

        $ta = $penempatan->tahunAkademikAktif();
        if (! $ta) {
            return redirect()->route('admin.mahasiswa')->with('error', 'Belum ada tahun akademik yang aktif.');
        }

        try {
            $r = $penempatan->buatUntukSemester((int) $ta->id_tahun_akademik);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.mahasiswa')->with('error', $e->getMessage());
        }

        $pesan = "Kelas {$ta->tahun_akademik}: {$r['dibuat']} mahasiswa ditempatkan, {$r['sudah-ada']} sudah punya kelas";
        foreach (['lulus' => 'sudah lulus', 'belum-mulai' => 'belum masuk', 'nim-tidak-valid' => 'dilewati karena format NIM salah'] as $kunci => $teks) {
            if ($r[$kunci] > 0) {
                $pesan .= ", {$r[$kunci]} {$teks}";
            }
        }

        return redirect()->route('admin.mahasiswa')->with('status', $pesan.'.');
    }

    /**
     * Aturan NIM: Memastikan format jenjang.prodi.tahun.kelas.absen (mis. 4.33.25.1.09)
     * dan wajib diawali digit '4' (D4 TRK).
     */
    private function aturanNim(Request $request, string $exceptId): array
    {
        $unik = function ($attr, $value, $fail) use ($exceptId) {
            if ($this->store->usedByOther('nim', $value, $exceptId)) {
                $fail($exceptId === '' ? 'NIM sudah terdaftar.' : 'NIM sudah dipakai mahasiswa lain.');
            }
        };

        return ['bail', 'required', function ($attr, $value, $fail) use ($request) {
            $angkatan = $request->filled('angkatan') ? (int) $request->input('angkatan') : null;
            if ($pesan = NimMahasiswa::kesalahan((string) $value, $angkatan)) {
                $fail($pesan);
            }
        }, $unik];
    }

    /**
     * Ubah status aktif / nonaktif mahasiswa.
     */
    public function toggleStatus(string $id): \Illuminate\Http\RedirectResponse
    {
        $success = $this->store->toggleStatus($id);
        
        abort_unless($success, 404);

        return redirect()->back()->with('status', 'Status mahasiswa berhasil diperbarui.');
    }
}