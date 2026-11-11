<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/**
 * Membuat kode tahan terhadap kolom yang belum ada di database bersama.
 * Kolom usulan (kurikulum.tahun_terbit, kurikulum.deskripsi, akreditasi.nomor_sk, ...) otomatis dipakai
 * begitu Kelompok 3 menambahkannya; sebelum itu dilewati tanpa error. Tidak perlu mengubah kode lagi.
 */
class KolomOpsional
{
    /** @var array<string, array<int, string>> */
    private static array $cache = [];

    public static function ada(string $tabel, string $kolom): bool
    {
        self::$cache[$tabel] ??= Schema::getColumnListing($tabel);

        return in_array($kolom, self::$cache[$tabel], true);
    }

    /** Buang kunci yang kolomnya tidak ada di tabel. */
    public static function saring(string $tabel, array $nilai): array
    {
        return array_filter($nilai, fn ($k) => self::ada($tabel, $k), ARRAY_FILTER_USE_KEY);
    }

    public static function lupakan(): void
    {
        self::$cache = [];
    }
}
