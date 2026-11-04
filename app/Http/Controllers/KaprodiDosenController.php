<?php

namespace App\Http\Controllers;

use App\Repositories\DosenStore;
use Carbon\Carbon;

/** Data dosen untuk kaprodi: HANYA BACA (tanpa tambah / ubah / hapus). Data sama dengan admin. */
class KaprodiDosenController extends Controller
{
    public function __construct(private DosenStore $store) {}

    /** Daftar dosen. (Hanya kolom yang dibutuhkan tabel, tidak mengirim hash sandi dll.) */
    public function index()
    {
        // TODO-DB: ganti dengan query tabel dosen (read only).
        $dosen = array_map(fn ($r) => [
            'id' => $r['id'],
            'nip' => $r['nip'],
            'nama' => $r['nama'],
            'jabatan' => $r['jabatan'],
            'golongan' => $r['golongan'],
            'status' => $r['status'],
        ], $this->store->all());

        return view('kaprodi.dosen', ['dosen' => $dosen, 'jabatan' => DosenStore::JABATAN]);
    }

    /** Detail satu dosen: profil, riwayat pangkat, dan aktivitas (4 tab). */
    public function show(string $id)
    {
        $row = $this->store->find($id);
        abort_unless($row, 404);

        $dosen = [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'nip' => $row['nip'],
            'status' => $row['status'],
            'foto' => null,   // URL foto dosen; null = tampil inisial
        ];

        $info = [
            ['Email akun', $row['email']],
            ['Role', DosenStore::ROLE[$row['role']] ?? 'Dosen'],
            ['Pangkat / golongan saat ini', $row['jabatan'].', '.$row['golongan']],
            ['Akun login', $row['status_akun'] === 'aktif' ? 'Aktif' : 'Nonaktif'],
        ];

        $riwayat = [];
        foreach ($row['riwayat'] as $i => $r) {
            $riwayat[] = [
                'pangkat' => $r['pangkat'],
                'golongan' => $r['golongan'],
                'sk' => $r['sk'],
                'tanggal' => Carbon::parse($r['tanggal'])->locale('id')->translatedFormat('j F Y'),
                'now' => $i === 0,
            ];
        }

        return view('kaprodi.dosen-detail', [
            'dosen' => $dosen,
            'info' => $info,
            'riwayat' => $riwayat,
            'aktivitas' => $this->aktivitas($row),
        ]);
    }

    /**
     * Aktivitas dosen (4 tab).
     * DIPAKSA KOSONG TOTAL UNTUK SEMUA DOSEN DAN SEMUA MODE.
     */
    private function aktivitas(array $row): array
    {
        return [
            'penelitian' => ['label' => 'Penelitian', 'items' => []],
            'pengabdian' => ['label' => 'Pengabdian', 'items' => []],
            'pengajaran' => ['label' => 'Pengajaran', 'items' => []],
            'kegiatan'   => ['label' => 'Kegiatan',   'items' => []],
        ];
    }
}
