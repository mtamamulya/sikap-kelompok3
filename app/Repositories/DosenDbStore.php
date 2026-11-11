<?php

namespace App\Repositories;

use App\Models\Dosen;
use App\Models\HistoriPangkatGolongan;
use App\Models\Role;
use App\Models\User;
use App\Support\LinkFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * Versi DATABASE dari DosenStore (tabel `dosen`, `users`, `histori_pangkat_golongan`).
 * Nama method dan bentuk array SAMA dengan versi JSON, jadi controller dan view tidak berubah.
 * Dipakai bila DATA_SOURCE=database (lihat DataSourceServiceProvider).
 *
 * Pemetaan: id <- id_dosen, nama <- dosen.nama, nip <- dosen.nip, email <- users.email,
 *           role (dosen|kaprodi) <- roles.nama_role, foto <- users.foto_profil,
 *           status DAN status_akun (aktif|nonaktif) <- users.status (boolean; satu kolom untuk keduanya),
 *           riwayat[] <- histori_pangkat_golongan (terbaru = tanggal_sk paling baru; jabatan/golongan = riwayat terbaru).
 * Golongan di aplikasi berformat "III/b", di database (enum) "3B"; dikonversi otomatis.
 * Tidak ada hapus: dosen dinonaktifkan lewat users.status (dosen direferensikan tabel kegiatan).
 */
class DosenDbStore extends DosenStore
{
    private const ROMAWI = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4];

    /** @var array<string, array<int, string>> */
    private array $enumCache = [];

    public function all(): array
    {
        return $this->query()->orderBy('id_dosen')->get()
            ->map(fn (Dosen $d) => $this->baris($d))->all();
    }

    public function find(string $id): ?array
    {
        $d = ctype_digit($id) ? $this->query()->find((int) $id) : null;

        return $d ? $this->baris($d) : null;
    }

    /** Apakah $field (nip|email) sudah dipakai dosen/akun LAIN. Email dicek ke SEMUA user. */
    public function usedByOther(string $field, string $value, string $exceptId): bool
    {
        $kecuali = $exceptId !== '' && ctype_digit($exceptId) ? (int) $exceptId : null;

        if ($field === 'nip') {
            return Dosen::query()->where('nip', $value)
                ->when($kecuali, fn ($q) => $q->where('id_dosen', '!=', $kecuali))
                ->exists();
        }

        if ($field === 'email') {
            $idUser = $kecuali ? Dosen::query()->whereKey($kecuali)->value('id_user') : null;

            return User::query()->where('email', $value)
                ->when($idUser, fn ($q) => $q->where('id_user', '!=', $idUser))
                ->exists();
        }

        return false;
    }

    public function exists(string $field, string $value): bool
    {
        return $this->usedByOther($field, $value, '');
    }

    /** Satu transaksi: users + dosen + riwayat pangkat pertama. */
    public function create(array $data): array
    {
        $this->cekEnumRiwayat($data['pangkat'], $data['golongan']);

        $id = DB::transaction(function () use ($data) {
            $u = new User;
            $u->forceFill([
                'id_role' => $this->idRole($data['role']),
                'email' => $data['email'],
                'password' => $data['password'],   // teks biasa: di-hash oleh cast 'hashed' pada model User
                'status' => $data['status'] === 'aktif',
                'foto_profil' => $data['foto'] ?? null,
            ])->save();

            $d = new Dosen;
            $d->forceFill([
                'id_user' => $u->id_user,
                'nip' => $data['nip'],
                'nama' => $data['nama'],
            ])->save();

            $this->tambahRiwayat($d, [
                'pangkat' => $data['pangkat'],
                'golongan' => $data['golongan'],
                'sk' => $data['sk'],
                'tanggal' => $data['tanggal'],
            ]);

            return $d->getKey();
        });

        return $this->baris($this->query()->find($id));
    }

    /**
     * Ubah dosen (satu transaksi). 'status_akun' dari form diabaikan: database hanya punya
     * users.status, yang diisi dari 'status' (status dosen).
     */
    public function update(string $id, array $data, ?array $riwayatBaru = null): ?array
    {
        if (! ctype_digit($id)) {
            return null;
        }
        if ($riwayatBaru) {
            $this->cekEnumRiwayat($riwayatBaru['pangkat'], $riwayatBaru['golongan']);
        }

        $ok = DB::transaction(function () use ($id, $data, $riwayatBaru) {
            $d = Dosen::query()->lockForUpdate()->find((int) $id);
            $u = $d ? User::query()->lockForUpdate()->find($d->id_user) : null;
            if (! $d || ! $u) {
                return false;
            }

            $d->forceFill(['nama' => $data['nama'], 'nip' => $data['nip']])->save();

            $u->forceFill([
                'email' => $data['email'],
                'id_role' => $this->idRole($data['role']),
                'status' => $data['status'] === 'aktif',
            ]);
            if (! empty($data['foto'])) {
                $u->foto_profil = $data['foto'];   // foto lama sudah dihapus oleh controller
            }
            if (! empty($data['password'])) {
                $u->password = $data['password'];
            }
            $u->save();

            if ($riwayatBaru) {
                $this->tambahRiwayat($d, $riwayatBaru);
            }

            return true;
        });

        return $ok ? $this->find($id) : null;
    }

    /**
     * Aktivitas dosen untuk 4 tab halaman detail.
     * Mengambil kegiatan langsung dari tabel `kegiatan`, `kategori`, dan `anggota_kegiatan`
     * tanpa melalukan join ke tabel `kegiatan_dosen` dan `skim` yang sudah dihapus.
     */
    public function aktivitas(string $id): ?array
    {
        $tab = [
            'penelitian' => ['label' => 'Penelitian', 'items' => []],
            'pengabdian' => ['label' => 'Pengabdian', 'items' => []],
            'pengajaran' => ['label' => 'Pengajaran', 'items' => []],
            'kegiatan' => ['label' => 'Kegiatan',   'items' => []],
        ];

        $idUser = ctype_digit($id) ? Dosen::query()->whereKey((int) $id)->value('id_user') : null;
        if (! $idUser) {
            return $tab;
        }

        // Query kegiatan tanpa join ke kegiatan_dosen dan skim
        $rows = DB::table('kegiatan as k')
            ->leftJoin('anggota_kegiatan as a', fn ($j) => $j->on('a.id_kegiatan', '=', 'k.id_kegiatan')->where('a.id_user', '=', $idUser))
            ->leftJoin('kategori as kt', 'kt.id_kategori', '=', 'k.id_kategori')
            ->where(fn ($q) => $q->where('k.id_user', $idUser)->orWhereNotNull('a.id_anggota_kegiatan'))
            ->orderByDesc('k.tanggal_mulai')->orderByDesc('k.id_kegiatan')
            ->get(['k.nama_kegiatan', 'k.tanggal_mulai', 'kt.nama_kategori', 'a.peran']);

        foreach ($rows as $r) {
            $tab['kegiatan']['items'][] = [
                'title' => $r->nama_kegiatan,
                'year' => $r->tanggal_mulai ? substr((string) $r->tanggal_mulai, 0, 4) : '-',
                'type' => $r->nama_kategori ?? '-',
                'role' => $r->peran ?? 'Pembuat',
            ];
        }

        // Query pengajaran dari enrollment kelas & mata kuliah
        $pengajaran = DB::table('enrollment_kelas_mata_kuliah as ekm')
            ->join('enrollment_mata_kuliah as emk', 'emk.id_enrollment_mk', '=', 'ekm.id_enrollment_mk')
            ->join('mata_kuliah as mk', 'mk.id_mk', '=', 'emk.id_mk')
            ->join('tahun_akademik as ta', 'ta.id_tahun_akademik', '=', 'emk.id_tahun_akademik')
            ->join('enrollment_tingkat_kelas as etk', 'etk.id_enrollment_tingkat_kelas', '=', 'ekm.id_enrollment_tingkat_kelas')
            ->join('kelas as kl', 'kl.id_kelas', '=', 'etk.id_kelas')
            ->join('tingkat as tg', 'tg.id_tingkat', '=', 'etk.id_tingkat')
            ->where('ekm.id_dosen', (int) $id)
            ->orderByDesc('ta.id_tahun_akademik')->orderBy('mk.nama_mk')
            ->get(['mk.nama_mk', 'mk.sks_teori', 'mk.sks_praktik', 'ta.tahun_akademik', 'tg.tingkat', 'kl.jenis_kelas']);

        foreach ($pengajaran as $r) {
            $tab['pengajaran']['items'][] = [
                'title' => $r->nama_mk,
                'year' => $r->tahun_akademik,
                'type' => 'Tingkat '.$r->tingkat.' Kelas '.$r->jenis_kelas.' · '.($r->sks_teori + $r->sks_praktik).' SKS',
                'role' => 'Pengampu',
            ];
        }

        return $tab;
    }

    /** Aktifkan / nonaktifkan dosen lewat users.status. Dosen aktif tampil di landing page. */
    public function toggleStatus(string $id): ?array
    {
        $dosen = ctype_digit($id) ? Dosen::query()->find((int) $id) : null;
        $user = $dosen ? User::query()->find($dosen->id_user) : null;
        if (! $user) {
            return null;
        }

        $user->status = ! $user->status;
        $user->save();

        return $this->find($id);
    }

    /** Dosen AKTIF untuk landing page: id, nama, jabatan (pangkat terbaru), dan URL foto (null bila belum ada). */
    public function publik(): ?array
    {
        return $this->query()
            ->whereHas('user', fn ($q) => $q->where('status', true))
            ->orderBy('id_dosen')->get()
            ->map(fn (Dosen $d) => [
                'id' => (string) $d->id_dosen,
                'nama' => $d->nama,
                'jabatan' => $d->historiPangkatGolongan->first()?->pangkat,
                'foto' => LinkFile::url($d->user?->foto_profil),
            ])->all();
    }

    /** Pilihan pangkat = isi enum histori_pangkat_golongan.pangkat (bila tidak terbaca, pakai daftar bawaan). */
    public function opsiPangkat(): array
    {
        return $this->enumValues('pangkat') ?: parent::opsiPangkat();
    }

    /** Pilihan golongan = isi enum histori_pangkat_golongan.golongan, ditampilkan berformat "III/b". */
    public function opsiGolongan(): array
    {
        $opsi = array_map(fn ($g) => self::golonganDariDb($g), $this->enumValues('golongan'));

        return $opsi ?: parent::opsiGolongan();
    }

    /** 'III/b' -> '3B' */
    public static function golonganKeDb(string $g): string
    {
        if (preg_match('/^(IV|I{1,3})\/([a-e])$/i', trim($g), $m)) {
            return self::ROMAWI[strtoupper($m[1])].strtoupper($m[2]);
        }

        return $g;
    }

    /** '3B' -> 'III/b' */
    public static function golonganDariDb(string $g): string
    {
        if (preg_match('/^([1-4])([A-Ea-e])$/', trim($g), $m)) {
            return array_flip(self::ROMAWI)[(int) $m[1]].'/'.strtolower($m[2]);
        }

        return $g;
    }

    private function query(): Builder
    {
        return Dosen::query()->with([
            'user.role',
            'historiPangkatGolongan' => fn ($q) => $q->orderByDesc('tanggal_sk')->orderByDesc('created_at'),
        ]);
    }

    private function baris(Dosen $d): array
    {
        $riwayat = $d->historiPangkatGolongan->map(fn ($h) => [
            'pangkat' => (string) $h->pangkat,
            'golongan' => self::golonganDariDb((string) $h->golongan),
            'sk' => (string) $h->nomor_sk,
            'tanggal' => substr((string) $h->tanggal_sk, 0, 10),
        ])->values()->all();

        $status = $d->user?->status ? 'aktif' : 'nonaktif';

        return [
            'id' => (string) $d->id_dosen,
            'nip' => $d->nip,
            'nama' => $d->nama,
            'jabatan' => $riwayat[0]['pangkat'] ?? '-',
            'golongan' => $riwayat[0]['golongan'] ?? '-',
            'status' => $status,
            'email' => (string) $d->user?->email,
            'role' => (string) $d->user?->role?->nama_role,
            'status_akun' => $status,
            'foto' => $d->user?->foto_profil,
            'riwayat' => $riwayat,
        ];
    }

    private function tambahRiwayat(Dosen $d, array $r): void
    {
        $h = new HistoriPangkatGolongan;
        $h->forceFill([
            'id_dosen' => $d->getKey(),
            'pangkat' => $r['pangkat'],
            'golongan' => self::golonganKeDb($r['golongan']),
            'nomor_sk' => $r['sk'],
            'tanggal_sk' => $r['tanggal'],
        ])->save();
    }

    /** Hanya role di DosenStore::ROLE (dosen|kaprodi) yang diterima; id dicari lewat nama, bukan angka mati. */
    private function idRole(string $nama): int
    {
        if (! array_key_exists($nama, self::ROLE)) {
            throw new RuntimeException("Role '{$nama}' tidak diizinkan untuk data dosen.");
        }

        $id = Role::query()->where('nama_role', $nama)->value('id_role');
        if (! $id) {
            throw new RuntimeException("Role '{$nama}' tidak ditemukan di tabel roles (jalankan DatabaseSeeder).");
        }

        return (int) $id;
    }

    /** Pastikan pangkat/golongan ada di enum database, supaya errornya ramah (bukan SQL error). */
    private function cekEnumRiwayat(string $pangkat, string $golongan): void
    {
        $cek = ['pangkat' => [$pangkat, $pangkat], 'golongan' => [self::golonganKeDb($golongan), $golongan]];

        foreach ($cek as $kolom => [$nilai, $tampil]) {
            $opsi = $this->enumValues($kolom);
            if ($opsi && ! in_array($nilai, $opsi, true)) {
                throw ValidationException::withMessages([
                    $kolom => ucfirst($kolom)." '{$tampil}' belum didukung database.",
                ]);
            }
        }
    }

    /**
     * Isi enum kolom. MySQL: dari tipe kolom (enum('a','b')). SQLite (database tes): enum disimpan sebagai
     * varchar + CHECK, jadi dibaca dari definisi tabel. Kosong bila keduanya tidak terbaca (pengecekan dilewati).
     */
    private function enumValues(string $kolom): array
    {
        if (isset($this->enumCache[$kolom])) {
            return $this->enumCache[$kolom];
        }

        $nilai = [];
        foreach (Schema::getColumns('histori_pangkat_golongan') as $c) {
            if ($c['name'] === $kolom) {
                $nilai = $this->ambilNilaiKutip((string) $c['type']);
            }
        }

        if (! $nilai && DB::connection()->getDriverName() === 'sqlite') {
            $ddl = (string) DB::selectOne(
                "select sql from sqlite_master where type = 'table' and name = ?",
                ['histori_pangkat_golongan']
            )?->sql;

            if (preg_match('/["`\[]?'.preg_quote($kolom, '/').'["`\]]?\s+in\s*\(([^)]*)\)/i', $ddl, $m)) {
                $nilai = $this->ambilNilaiKutip($m[1]);
            }
        }

        return $this->enumCache[$kolom] = $nilai;
    }

    /** @return array<int, string> semua nilai di antara tanda kutip tunggal. */
    private function ambilNilaiKutip(string $teks): array
    {
        preg_match_all("/'((?:[^']|'')*)'/", $teks, $m);

        return array_map(fn ($v) => str_replace("''", "'", $v), $m[1]);
    }
}
