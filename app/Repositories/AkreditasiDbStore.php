<?php

namespace App\Repositories;

use App\Models\Akreditasi;
use App\Models\User;
use App\Support\KolomOpsional;
use App\Support\LinkFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Versi DATABASE dari AkreditasiStore (tabel `akreditasi`). Nama method dan bentuk array sama dengan versi JSON.
 * Aturan tetap: selalu satu akreditasi aktif; yang aktif tidak bisa dihapus.
 *
 * Pemetaan: id <- id_akreditasi, lembaga <- keterangan (varchar 50, wajib), year <- tahun_terbit,
 *           berlaku_sampai <- berlaku_sampai, status (aktif|nonaktif) <- status (boolean), file <- link,
 *           nomor_sk <- nomor_sk* (kolom usulan; dilewati otomatis bila belum ada). Peringkat sudah dihapus dari aplikasi.
 * id_user (wajib) diisi pengguna yang login.
 */
class AkreditasiDbStore extends AkreditasiStore
{
    public function all(): array
    {
        return Akreditasi::query()->orderByDesc('id_akreditasi')->get()
            ->map(fn (Akreditasi $a) => $this->baris($a))->all();
    }

    public function find(string $id): ?array
    {
        $a = ctype_digit($id) ? Akreditasi::query()->find((int) $id) : null;

        return $a ? $this->baris($a) : null;
    }

    public function create(array $data, bool $aktifkan, ?UploadedFile $pdf = null): array
    {
        return DB::transaction(function () use ($data, $aktifkan, $pdf) {
            $jadiAktif = $aktifkan || ! Akreditasi::query()->exists();

            if ($jadiAktif) {
                Akreditasi::query()->update(['status' => false]);
            }

            $a = new Akreditasi;
            $a->forceFill(KolomOpsional::saring('akreditasi', [
                'id_user' => $this->idUser(),
                'keterangan' => $data['lembaga'],
                'nomor_sk' => ($data['nomor_sk'] ?? '') !== '' ? $data['nomor_sk'] : null,
                'tahun_terbit' => (int) $data['year'],
                'berlaku_sampai' => ($data['berlaku_sampai'] ?? '') !== '' ? $data['berlaku_sampai'] : null,
                'status' => $jadiAktif,
                'link' => $pdf ? $pdf->store('akreditasi', 'public') : null,
            ]))->save();

            return $this->baris($a);
        });
    }

    public function update(string $id, array $data, ?UploadedFile $pdf = null): ?array
    {
        $a = ctype_digit($id) ? Akreditasi::query()->find((int) $id) : null;
        if (! $a) {
            return null;
        }

        $a->forceFill(KolomOpsional::saring('akreditasi', [
            'keterangan' => $data['lembaga'],
            'tahun_terbit' => (int) $data['year'],
            'nomor_sk' => ($data['nomor_sk'] ?? '') !== '' ? $data['nomor_sk'] : null,
            'berlaku_sampai' => ($data['berlaku_sampai'] ?? '') !== '' ? $data['berlaku_sampai'] : null,
        ]));
        if ($pdf) {
            LinkFile::hapus($a->link);
            $a->link = $pdf->store('akreditasi', 'public');
        }
        $a->save();

        return $this->baris($a);
    }

    /** Jadikan $id satu-satunya yang aktif. */
    /** Jadikan $id satu-satunya yang aktif. */
    public function activate(string $id): ?array
    {
        $a = ctype_digit($id) ? Akreditasi::query()->find((int) $id) : null;
        if (! $a) {
            return null;
        }

        DB::transaction(function () use ($a) {
            Akreditasi::query()->whereKeyNot($a->getKey())->update(['status' => false]);
            Akreditasi::query()->whereKey($a->getKey())->update(['status' => true]);
        });

        return $this->baris($a->refresh());
    }

    /** Hasil: 'ok' | 'aktif' (yang aktif tidak boleh dihapus) | 'tidak-ada'. */
    public function delete(string $id): string
    {
        $a = ctype_digit($id) ? Akreditasi::query()->find((int) $id) : null;

        if (! $a) {
            return 'tidak-ada';
        }
        if ($a->getAttribute('status')) {
            return 'aktif';
        }

        LinkFile::hapus($a->link);
        $a->delete();

        return 'ok';
    }

    public function pdfUrl(array $row): ?string
    {
        return LinkFile::url($row['file'] ?? null);
    }

    private function baris(Akreditasi $a): array
    {
        $berlaku = $a->getAttribute('berlaku_sampai');

        return [
            'id' => (string) $a->id_akreditasi,
            'lembaga' => (string) $a->keterangan,
            'peringkat' => '',
            'nomor_sk' => (string) $a->getAttribute('nomor_sk'),
            'year' => (string) $a->getAttribute('tahun_terbit'),
            'berlaku_sampai' => $berlaku ? substr((string) $berlaku, 0, 10) : '',
            'status' => $a->getAttribute('status') ? 'aktif' : 'nonaktif',
            'file' => $a->link,
            'created_at' => $a->created_at?->toDateTimeString(),
        ];
    }

    /** akreditasi.id_user wajib: pengguna yang login; bila belum ada (login simulasi), pakai admin pertama. */
    private function idUser(): int
    {
        $id = Auth::id() ?? User::query()
            ->whereHas('role', fn ($q) => $q->whereRaw('LOWER(nama_role) = ?', ['admin']))
            ->orderBy('id_user')->value('id_user');

        if (! $id) {
            throw new RuntimeException('Tidak ada pengguna admin di tabel users (jalankan DatabaseSeeder).');
        }

        return (int) $id;
    }
}
