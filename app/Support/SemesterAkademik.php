<?php

namespace App\Support;

/**
 * Menghitung semester dan tingkat mahasiswa dari tahun masuk dan tahun akademik ("Ganjil 2026/2027").
 * Dihitung dari tahun AWAL tahun akademik, bukan tanggal hari ini:
 *   semester = (tahun_awal - tahun_masuk) * 2 + (1 bila Ganjil, 2 bila Genap)
 *   tingkat  = ceil(semester / 2)      -> naik hanya di semester Ganjil
 */
class SemesterAkademik
{
    /** "Ganjil 2026/2027" -> ['jenis' => 'ganjil', 'tahun_awal' => 2026]; format lain -> null. */
    public static function parse(string $tahunAkademik): ?array
    {
        if (! preg_match('/^(Ganjil|Genap)\s+(\d{4})\/(\d{4})$/i', trim($tahunAkademik), $m)) {
            return null;
        }
        if ((int) $m[3] !== (int) $m[2] + 1) {
            return null;
        }

        return ['jenis' => strtolower($m[1]), 'tahun_awal' => (int) $m[2]];
    }

    /** Semester ke-n (1 = Ganjil tahun masuk). Nilai kurang dari 1 berarti belum masuk. $ta = hasil parse(). */
    public static function semester(int $tahunMasuk, array $ta): int
    {
        return ($ta['tahun_awal'] - $tahunMasuk) * 2 + ($ta['jenis'] === 'ganjil' ? 1 : 2);
    }

    public static function tingkat(int $semester): int
    {
        return intdiv($semester + 1, 2);
    }
}