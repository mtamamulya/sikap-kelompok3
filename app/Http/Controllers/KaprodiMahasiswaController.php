<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Repositories\MahasiswaStore;
use Illuminate\Support\Str;

/** Data mahasiswa untuk kaprodi: HANYA BACA (tanpa tambah / ubah / hapus). Data sama dengan admin. */
class KaprodiMahasiswaController extends Controller
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

        return view('kaprodi.mahasiswa', ['students' => $students, 'levels' => MahasiswaStore::TINGKAT]);
    }

    /** Detail satu mahasiswa. */
    public function show(string $id)
    {
        $row = $this->store->find($id);
        abort_unless($row, 404);

        // 1. Ambil data foto dari tabel users secara langsung menggunakan relasi Eloquent
        $mhsModel = Mahasiswa::with('user')->find($id);
        $fotoProfil = $mhsModel && $mhsModel->user ? $mhsModel->user->foto_profil : null;

        // 2. Sisipkan key 'foto' ke dalam array mahasiswa
        $mahasiswa = [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'nim' => $row['nim'],
            'status' => $row['status'],
            'foto' => $fotoProfil,
        ];

        $academic = [
            ['Angkatan', $row['angkatan']],
            ['Program studi', config('admin.prodi.nama', 'Teknologi Rekayasa Komputer').', '.config('admin.prodi.jenjang', 'D4')],
            ['Tingkat saat ini', $row['level']],
            ['Kelas saat ini', Str::after($row['kelas'], 'TI-')],
        ];

        $identity = [
            ['Email akun', $row['email']],
            ['Akun login', $row['status'] === 'Non-aktif' ? 'Nonaktif' : 'Aktif'],
        ];

        // 3. Fallback dummy data ikut dirapikan agar jika database kosong, yang muncul tetap TRK D4 (bukan Teknik Informatika)
        $history = $this->store->riwayatKelas($id) ?? [
            ['year' => '2025/2026', 'current' => true, 'sem' => 'Ganjil', 'prodi' => config('admin.prodi.nama', 'Teknologi Rekayasa Komputer'), 'level' => $row['level'], 'cls' => Str::after($row['kelas'], 'TI-')],
            ['year' => '2024/2025', 'sem' => 'Genap',  'prodi' => config('admin.prodi.nama', 'Teknologi Rekayasa Komputer'), 'level' => 'Tingkat 1', 'cls' => '1B'],
            ['year' => '2024/2025', 'sem' => 'Ganjil', 'prodi' => config('admin.prodi.nama', 'Teknologi Rekayasa Komputer'), 'level' => 'Tingkat 1', 'cls' => '1B'],
        ];

        $kegiatan = $this->store->kegiatan($id) ?? [
            ['title' => 'Kompetisi Pengembangan Aplikasi Web', 'year' => '2026', 'type' => 'Lomba',    'role' => 'Ketua Tim'],
            ['title' => 'Pelatihan Dasar Jaringan Komputer',   'year' => '2025', 'type' => 'Pelatihan', 'role' => 'Peserta'],
            ['title' => 'Seminar Nasional Teknologi Informasi', 'year' => '2025', 'type' => 'Seminar',  'role' => 'Panitia'],
        ];

        return view('kaprodi.mahasiswa-detail', compact('mahasiswa', 'academic', 'identity', 'history', 'kegiatan'));
    }
}
