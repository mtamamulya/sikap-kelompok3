<?php

namespace App\Repositories;

use App\Models\Kurikulum;
use App\Models\MataKuliah;

/**
 * Sumber data halaman PUBLIK Mata Kuliah dari DATABASE (tabel kurikulum + mata_kuliah).
 * Dipakai bila DATA_SOURCE=database (lihat AppServiceProvider).
 */
class EloquentMataKuliahRepository implements MataKuliahRepository
{
    public function kurikulum(): array
    {
        return Kurikulum::query()
            ->where('status', true)
            ->whereHas('mataKuliah')                       // kurikulum kosong tidak tampil
            ->get()
            ->sortByDesc(fn (Kurikulum $k) => [(string) $k->getAttribute('tahun_terbit'), $k->id_kurikulum])
            ->map(fn (Kurikulum $k) => ['id' => (string) $k->id_kurikulum, 'nama' => $k->nama_kurikulum])
            ->values()
            ->all();
    }

    public function semester(string $kurikulumId): array
    {
        if (! ctype_digit($kurikulumId)) {
            return [];
        }

        return MataKuliah::query()
            ->where('id_kurikulum', (int) $kurikulumId)
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester')
            ->map(fn ($s) => (int) $s)
            ->all();
    }

    public function matkul(string $kurikulumId, int $semester): array
    {
        if (! ctype_digit($kurikulumId)) {
            return [];
        }

        return MataKuliah::query()
            ->where('id_kurikulum', (int) $kurikulumId)
            ->where('semester', $semester)
            ->get()
            ->sort(fn (MataKuliah $a, MataKuliah $b) => strnatcasecmp($a->kode_mk, $b->kode_mk))
            ->map(fn (MataKuliah $m) => [
                'kode' => $m->kode_mk,
                'nama' => $m->nama_mk,
                'sks_teori' => (int) $m->sks_teori,
                'sks_praktek' => (int) $m->sks_praktik,
                'jam_teori' => (int) $m->jam_teori,
                'jam_praktek' => (int) $m->jam_praktek,
            ])
            ->values()
            ->all();
    }
}
