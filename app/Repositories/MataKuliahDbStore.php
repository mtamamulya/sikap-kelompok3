<?php

namespace App\Repositories;

use App\Models\MataKuliah;

/**
 * Versi DATABASE dari MataKuliahStore (tabel `mata_kuliah`). Nama method dan bentuk array sama dengan versi JSON.
 *
 * Pemetaan: id <- id_mk, kurikulum <- id_kurikulum (string), kode <- kode_mk, nama <- nama_mk,
 *           sks_praktek <- sks_praktik (perhatikan ejaan di database), jam_praktek <- jam_praktek.
 * Method turunan (semesters, bySemester, listing, firstSemester, ...) bekerja dari forKurikulum().
 */
class MataKuliahDbStore extends MataKuliahStore
{
    public function all(): array
    {
        return MataKuliah::query()->orderBy('id_mk')->get()
            ->map(fn (MataKuliah $m) => $this->baris($m))->all();
    }

    public function find(int $id): ?array
    {
        $m = MataKuliah::query()->find($id);

        return $m ? $this->baris($m) : null;
    }

    public function forKurikulum(string $kurikulumId): array
    {
        if (! ctype_digit($kurikulumId)) {
            return [];
        }

        return MataKuliah::query()->where('id_kurikulum', (int) $kurikulumId)
            ->orderBy('semester')->orderBy('kode_mk')->get()
            ->map(fn (MataKuliah $m) => $this->baris($m))->all();
    }

    public function count(string $kurikulumId): int
    {
        return ctype_digit($kurikulumId) ? MataKuliah::query()->where('id_kurikulum', (int) $kurikulumId)->count() : 0;
    }

    public function kodeExists(string $kurikulumId, string $kode, ?int $exceptId = null): bool
    {
        if (! ctype_digit($kurikulumId)) {
            return false;
        }

        return MataKuliah::query()
            ->where('id_kurikulum', (int) $kurikulumId)
            ->where('kode_mk', $kode)                       // collation utf8mb4_unicode_ci: tidak peka huruf besar/kecil
            ->when($exceptId, fn ($q) => $q->where('id_mk', '!=', $exceptId))
            ->exists();
    }

    public function create(array $data): array
    {
        $m = new MataKuliah;
        $m->forceFill($this->kolom($data))->save();

        return $this->baris($m);
    }

    public function update(int $id, array $data): ?array
    {
        $m = MataKuliah::query()->find($id);
        if (! $m) {
            return null;
        }

        $m->forceFill($this->kolom($data))->save();

        return $this->baris($m);
    }

    public function delete(int $id): bool
    {
        return MataKuliah::query()->whereKey($id)->delete() > 0;
    }

    /** Di database sudah ditangani cascadeOnDelete, tetap dibuat aman bila dipanggil langsung. */
    public function deleteByKurikulum(string $kurikulumId): void
    {
        if (ctype_digit($kurikulumId)) {
            MataKuliah::query()->where('id_kurikulum', (int) $kurikulumId)->delete();
        }
    }

    private function kolom(array $d): array
    {
        return [
            'id_kurikulum' => (int) $d['kurikulum'],
            'semester' => (int) $d['semester'],
            'kode_mk' => trim($d['kode']),
            'nama_mk' => trim($d['nama']),
            'sks_teori' => (int) $d['sks_teori'],
            'sks_praktik' => (int) $d['sks_praktek'],
            'jam_teori' => (int) $d['jam_teori'],
            'jam_praktek' => (int) $d['jam_praktek'],
        ];
    }

    private function baris(MataKuliah $m): array
    {
        return [
            'id' => (int) $m->id_mk,
            'kurikulum' => (string) $m->id_kurikulum,
            'semester' => (int) $m->semester,
            'kode' => $m->kode_mk,
            'nama' => $m->nama_mk,
            'sks_teori' => (int) $m->sks_teori,
            'sks_praktek' => (int) $m->sks_praktik,
            'jam_teori' => (int) $m->jam_teori,
            'jam_praktek' => (int) $m->jam_praktek,
            'created_at' => $m->created_at?->toDateTimeString(),
        ];
    }
}
