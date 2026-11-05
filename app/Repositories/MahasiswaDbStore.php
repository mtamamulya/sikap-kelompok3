<?php

namespace App\Repositories;

use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;
use App\Support\LinkFile;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Versi DATABASE dari MahasiswaStore (tabel `mahasiswa`, `users`, dan enrollment kelas).
 * Nama method dan bentuk array SAMA dengan versi JSON, jadi controller dan view tidak berubah.
 * Dipakai bila DATA_SOURCE=database (lihat DataSourceServiceProvider).
 *
 * Pemetaan: id <- id_mahasiswa, nim <- nim, nama <- nama, angkatan <- tahun_masuk, email <- users.email,
 *           foto <- users.foto_profil, status <- users.status (boolean: Aktif | Non-aktif),
 *           level ("Tingkat 2") dan kelas ("TI-2B") <- enrollment tahun akademik aktif ("-" bila belum ditempatkan).
 * Cuti dan Lulus belum punya kolom: bila dipilih, disimpan sebagai Non-aktif. tanggal_lahir dan telepon
 * belum punya kolom: tidak disimpan (dikembalikan null).
 * Mahasiswa baru langsung ditempatkan ke kelas pada tahun akademik aktif (lihat PenempatanKelasMahasiswa).
 * Mengubah NIM/angkatan TIDAK memindahkan kelas yang sudah tercatat (kelas diubah lewat enrollment).
 */
class MahasiswaDbStore extends MahasiswaStore
{
    public function __construct(private PenempatanKelasMahasiswa $penempatan) {}

    public function all(): array
    {
        $kelas = $this->penempatan->kelasSaatIniSemua();

        return Mahasiswa::query()->with('user')->orderBy('id_mahasiswa')->get()
            ->map(fn (Mahasiswa $m) => $this->baris($m, $kelas[$m->id_mahasiswa] ?? null))->all();
    }

    public function find(string $id): ?array
    {
        $m = ctype_digit($id) ? Mahasiswa::query()->with('user')->find((int) $id) : null;

        return $m ? $this->baris($m, $this->penempatan->kelasSaatIni((int) $m->id_mahasiswa)) : null;
    }

    /** Apakah $field (nim|email) sudah dipakai mahasiswa/akun LAIN. Email dicek ke SEMUA user. */
    public function usedByOther(string $field, string $value, string $exceptId): bool
    {
        $kecuali = $exceptId !== '' && ctype_digit($exceptId) ? (int) $exceptId : null;

        if ($field === 'nim') {
            return Mahasiswa::query()->where('nim', $value)
                ->when($kecuali, fn ($q) => $q->where('id_mahasiswa', '!=', $kecuali))
                ->exists();
        }

        if ($field === 'email') {
            $idUser = $kecuali ? Mahasiswa::query()->whereKey($kecuali)->value('id_user') : null;

            return User::query()->where('email', $value)
                ->when($idUser, fn ($q) => $q->where('id_user', '!=', $idUser))
                ->exists();
        }

        return false;
    }

    /** Satu transaksi: users + mahasiswa + penempatan kelas pada tahun akademik aktif. */
    public function create(array $data): array
    {
        $id = DB::transaction(function () use ($data) {
            $u = new User;
            $u->forceFill([
                'id_role' => $this->idRole(),
                'email' => $data['email'],
                'password' => $data['password'],   // teks biasa: di-hash oleh cast 'hashed' pada model User
                'status' => $this->aktif($data['status'] ?? null),
            ])->save();

            $m = new Mahasiswa;
            $m->forceFill([
                'id_user' => $u->id_user,
                'nim' => $data['nim'],
                'nama' => $data['nama'],
                'tahun_masuk' => (int) $data['angkatan'],
            ])->save();

            // AMBIL BATAS TAHUN AKADEMIK AKTIF
            $taAktif = $this->penempatan->tahunAkademikAktif();
            
            if ($taAktif) {
                // Hitung skor kronologis tahun aktif (misal: Ganjil 2026 = 2026*2+1)
                $aktifParsed = \App\Support\SemesterAkademik::parse($taAktif->tahun_akademik);
                $aktifNilai = ($aktifParsed['tahun_awal'] * 2) + ($aktifParsed['jenis'] === 'ganjil' ? 1 : 2);

                $semuaTa = \Illuminate\Support\Facades\DB::table('tahun_akademik')->get();

                foreach ($semuaTa as $ta) {
                    $taParsed = \App\Support\SemesterAkademik::parse($ta->tahun_akademik);
                    if ($taParsed) {
                        // Hitung skor kronologis tahun yang sedang di-loop
                        $taNilai = ($taParsed['tahun_awal'] * 2) + ($taParsed['jenis'] === 'ganjil' ? 1 : 2);
                        
                        // Hanya buatkan kelas jika tahun tersebut <= tahun aktif (secara waktu, bukan ID)
                        if ($taNilai <= $aktifNilai) {
                            $this->penempatan->tempatkan($m, $ta);
                        }
                    }
                }
            }

            return $m->getKey();
        });

        return $this->find((string) $id);
    }

    /** Password hanya diganti bila terisi; foto hanya diubah bila key 'foto' ada (path baru, atau null untuk menghapus). */
    public function update(string $id, array $data): ?array
    {
        if (! ctype_digit($id)) {
            return null;
        }

        $ok = DB::transaction(function () use ($id, $data) {
            $m = Mahasiswa::query()->lockForUpdate()->find((int) $id);
            $u = $m ? User::query()->lockForUpdate()->find($m->id_user) : null;
            if (! $m || ! $u) {
                return false;
            }

            $m->forceFill([
                'nama' => $data['nama'],
                'nim' => $data['nim'],
                'tahun_masuk' => (int) $data['angkatan'],
            ])->save();

            $u->forceFill([
                'email' => $data['email'],
                'status' => $this->aktif($data['status'] ?? null),
            ]);
            if (! empty($data['password'])) {
                $u->password = $data['password'];
            }
            if (array_key_exists('foto', $data)) {
                $u->foto_profil = $data['foto'];   // foto lama sudah dihapus oleh controller
            }
            $u->save();

            return true;
        });

        return $ok ? $this->find($id) : null;
    }

    /**
     * Hapus mahasiswa beserta akunnya dan riwayat kelasnya (satu transaksi).
     * Hasil: 'ok' | 'dipakai' (masih dipakai data lain, mis. prestasi: nonaktifkan saja) | 'tidak-ada'.
     */
    public function delete(string $id): string
    {
        $m = ctype_digit($id) ? Mahasiswa::query()->find((int) $id) : null;
        if (! $m) {
            return 'tidak-ada';
        }

        $foto = null;

        try {
            DB::transaction(function () use ($m, &$foto) {
                $u = User::query()->find($m->id_user);
                $foto = $u?->foto_profil;

                DB::table('enrollment_mahasiswa_tingkat_kelas')->where('id_mahasiswa', $m->getKey())->delete();
                $m->delete();
                $u?->delete();
            });
        } catch (QueryException) {
            return 'dipakai';
        }

        LinkFile::hapus($foto);

        return 'ok';
    }

    /** Pilihan status yang bisa disimpan database (Cuti dan Lulus belum punya kolom). */
    public function opsiStatus(): array
    {
        return ['Aktif', 'Non-aktif'];
    }

    private function baris(Mahasiswa $m, ?array $kelas): array
    {
        return [
            'id' => (string) $m->id_mahasiswa,
            'nim' => $m->nim,
            'nama' => $m->nama,
            'level' => $kelas ? 'Tingkat '.$kelas['tingkat'] : '-',
            'kelas' => $kelas['label'] ?? '-',
            'status' => $m->user?->status ? 'Aktif' : 'Non-aktif',
            'angkatan' => (string) $m->tahun_masuk,
            'email' => (string) $m->user?->email,
            'tanggal_lahir' => null,
            'telepon' => null,
            'foto' => $m->user?->foto_profil,
        ];
    }

    private function aktif(?string $status): bool
    {
        return ($status ?? 'Aktif') === 'Aktif';
    }

    private function idRole(): int
    {
        $id = Role::query()->where('nama_role', 'mahasiswa')->value('id_role');
        if (! $id) {
            throw new RuntimeException("Role 'mahasiswa' tidak ditemukan di tabel roles (jalankan DatabaseSeeder).");
        }

        return (int) $id;
    }

    public function riwayatKelas(string $id): ?array
    {
        if (!ctype_digit($id)) {
            return null;
        }

        // Ambil riwayat kelas asli dari database (Hapus orderByDesc id)
        $rows = \Illuminate\Support\Facades\DB::table('enrollment_mahasiswa_tingkat_kelas as emt')
            ->join('enrollment_tingkat_kelas as etk', 'etk.id_enrollment_tingkat_kelas', '=', 'emt.id_enrollment_tingkat_kelas')
            ->join('tahun_akademik as ta', 'ta.id_tahun_akademik', '=', 'etk.id_tahun_akademik')
            ->join('tingkat as tg', 'tg.id_tingkat', '=', 'etk.id_tingkat')
            ->join('kelas as kl', 'kl.id_kelas', '=', 'etk.id_kelas')
            ->join('prodi as pr', 'pr.id_prodi', '=', 'etk.id_prodi')
            ->where('emt.id_mahasiswa', (int) $id)
            ->get();

        if ($rows->isEmpty()) {
            return null;
        }

        return $rows->map(function ($r) {
            $parts = explode(' ', $r->tahun_akademik);
            return [
                'year' => $parts[1] ?? $r->tahun_akademik,
                'sem' => $parts[0] ?? '-',
                'prodi' => $r->nama_prodi,
                'level' => 'Tingkat ' . $r->tingkat,
                'cls' => $r->tingkat . $r->jenis_kelas,
                'current' => (bool) $r->status,
            ];
        })->sortByDesc(function ($item) {
            // Urutkan ulang di PHP: Tahun turun, lalu Genap (2) > Ganjil (1)
            $bobotSemester = $item['sem'] === 'Genap' ? 2 : 1;
            return $item['year'] . '-' . $bobotSemester;
        })->values()->all();
    }

    public function kegiatan(string $id): ?array
    {
        // Mengembalikan array kosong agar tampilan Blade memunculkan tulisan "Belum ada kegiatan"
        return []; 
    }
}