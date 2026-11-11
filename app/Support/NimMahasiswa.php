<?php

namespace App\Support;

/**
 * Aturan NIM mahasiswa: jenjang.prodi.tahun.kelas.absen, contoh 4.33.25.1.09
 *   4  = jenjang (3 = D3, 4 = D4)      33 = kode prodi TRK        25 = tahun masuk (2025)
 *   1  = kelas (0 = A, 1 = B, 2 = C)   09 = nomor absen
 * Kode prodi, jumlah kelas, dan awalan label kelas bisa diatur lewat config('admin.prodi.*'):
 * kode_nim (bawaan '33'), jumlah_kelas (bawaan 3), kode_kelas (bawaan 'TI', untuk label "TI-2B").
 */
class NimMahasiswa
{
    private const JENJANG = ['3' => 'D3', '4' => 'D4'];

    private const HURUF_KELAS = ['A', 'B', 'C', 'D'];

    /** Pecah NIM menurut format saja (tanpa memeriksa isinya). Null bila formatnya salah. */
    public static function uraikan(string $nim): ?array
    {
        if (! preg_match('/^(\d)\.(\d{2})\.(\d{2})\.(\d)\.(\d{2})$/', trim($nim), $m)) {
            return null;
        }

        return [
            'jenjang_kode' => $m[1],
            'kode_prodi' => $m[2],
            'tahun_masuk' => 2000 + (int) $m[3],
            'kelas_digit' => (int) $m[4],
            'absen' => $m[5],
        ];
    }

    /** NIM valid -> ['jenjang' => 'D4', 'kode_prodi' => '33', 'tahun_masuk' => 2025, 'kelas' => 'B', 'absen' => '09']; selain itu null. */
    public static function parse(string $nim): ?array
    {
        $p = self::uraikan($nim);
        if (! $p || self::kesalahan($nim) !== null) {
            return null;
        }

        return [
            'jenjang' => self::JENJANG[$p['jenjang_kode']],
            'kode_prodi' => $p['kode_prodi'],
            'tahun_masuk' => $p['tahun_masuk'],
            'kelas' => self::HURUF_KELAS[$p['kelas_digit']],
            'absen' => $p['absen'],
        ];
    }

    /** Pesan kesalahan untuk validasi form; null bila NIM benar (dan cocok dengan $angkatan bila diberikan). */
    public static function kesalahan(string $nim, ?int $angkatan = null): ?string
    {
        $p = self::uraikan($nim);

        if (! $p) {
            return 'Format NIM harus jenjang.prodi.tahun.kelas.absen, contoh 4.33.25.1.09.';
        }
        if ($p['jenjang_kode'] !== '4') {
            return 'Kode jenjang pada NIM harus 4 (D4 Teknologi Rekayasa Komputer).';
        }
        if (! isset(self::JENJANG[$p['jenjang_kode']])) {
            return 'Kode jenjang pada NIM tidak dikenal (3 = D3, 4 = D4).';
        }
        if ($p['kode_prodi'] !== self::kodeProdi()) {
            return 'Kode prodi pada NIM harus '.self::kodeProdi().'.';
        }
        if ($p['kelas_digit'] >= self::jumlahKelas()) {
            return 'Kode kelas pada NIM harus 0 sampai '.(self::jumlahKelas() - 1).'.';
        }
        if ($angkatan !== null && $p['tahun_masuk'] !== $angkatan) {
            return 'Tahun pada NIM ('.$p['tahun_masuk'].') tidak sama dengan angkatan ('.$angkatan.').';
        }

        return null;
    }

    /** Semester terakhir sebelum lulus: D3 = 6, D4 = 8. */
    public static function maxSemester(string $jenjang): int
    {
        return $jenjang === 'D3' ? 6 : 8;
    }

    /** Label kelas, contoh "TI-2B". */
    public static function label(int $tingkat, string $kelas): string
    {
        return config('admin.prodi.kode_kelas', 'TI').'-'.$tingkat.$kelas;
    }

    private static function kodeProdi(): string
    {
        return (string) config('admin.prodi.kode_nim', '33');
    }

    private static function jumlahKelas(): int
    {
        return min(count(self::HURUF_KELAS), max(1, (int) config('admin.prodi.jumlah_kelas', 3)));
    }
}