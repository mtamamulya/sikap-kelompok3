<?php

namespace App\Http\Controllers;

use App\Repositories\DosenStore;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Pastikan import DB di bagian atas file
use Illuminate\Support\Facades\Storage; // Pastikan Storage di-import

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
            'foto' => ! empty($row['foto']) ? Storage::disk('public')->url($row['foto']) : null,
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
     * Mengambil data asli dari database melalui tabel anggota_kegiatan.
     */
    private function aktivitas(array $row): array
    {
        $tabs = [
            'penelitian' => ['label' => 'Penelitian', 'items' => []],
            'pengabdian' => ['label' => 'Pengabdian', 'items' => []],
            'pengajaran' => ['label' => 'Pengajaran', 'items' => []],
            'kegiatan' => ['label' => 'Kegiatan',   'items' => []],
        ];

        if (isset($row['aktivitas'])) {
            return $row['aktivitas'];
        }

        try {
            $idUser = $row['id_user'] ?? $row['id'];

            // Gunakan Query Builder untuk melakukan JOIN secara manual sesuai struktur tabel yang dikirim
            $kegiatans = DB::table('anggota_kegiatan')
                ->join('kegiatan', 'anggota_kegiatan.id_kegiatan', '=', 'kegiatan.id_kegiatan')
                ->leftJoin('kategori', 'kegiatan.id_kategori', '=', 'kategori.id_kategori')
                ->leftJoin('jenis_kegiatan', 'kategori.id_jenis_kegiatan', '=', 'jenis_kegiatan.id_jenis_kegiatan')
                ->where('anggota_kegiatan.id_user', $idUser)
                ->orderBy('kegiatan.tanggal_mulai', 'desc')
                ->select(
                    'kegiatan.nama_kegiatan',
                    'kegiatan.tanggal_mulai',
                    'kategori.nama_kategori',
                    'jenis_kegiatan.nama_jenis_kegiatan',
                    'anggota_kegiatan.peran'
                )
                ->get();

            foreach ($kegiatans as $keg) {
                // Tentukan ranah tab berdasarkan nama_kategori (misal 'Penelitian', 'Pengabdian').
                // Jika tidak spesifik, masukkan ke tab 'kegiatan' umum.
                $kategoriNama = strtolower(trim($keg->nama_kategori ?? ''));

                $ranah = 'kegiatan'; // Default
                if (str_contains($kategoriNama, 'penelitian')) {
                    $ranah = 'penelitian';
                } elseif (str_contains($kategoriNama, 'pengabdian')) {
                    $ranah = 'pengabdian';
                } elseif (str_contains($kategoriNama, 'pengajaran')) {
                    $ranah = 'pengajaran';
                }

                $tabs[$ranah]['items'][] = [
                    'judul' => $keg->nama_kegiatan,
                    'tahun' => $keg->tanggal_mulai ? date('Y', strtotime($keg->tanggal_mulai)) : '-',
                    'jenis' => $keg->nama_kategori ?? '-',
                    'peran' => $keg->peran ?? 'Anggota',
                ];
            }
        } catch (\Exception $e) {
            // Cegah error jika tabel-tabel tersebut belum di-migrate sepenuhnya
        }

        return $tabs;
    }
}
