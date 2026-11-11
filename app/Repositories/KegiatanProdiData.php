<?php

namespace App\Repositories;

/**
 * Data contoh kegiatan tingkat program studi (KKL, kuliah tamu, studi banding, dst.).
 * Dipakai bersama oleh halaman Kegiatan (tab Prodi) dan laporan PDF/Excel.
 *
 * Aturan: kegiatan prodi dibuat langsung oleh admin/kaprodi, jadi statusnya selalu "Disetujui".
 * TODO-DB (Fase C): ganti isi semua() dengan query tabel kegiatan (jenis Prodi); bentuk baris tetap.
 */
class KegiatanProdiData
{
    public const DISETUJUI = 'Disetujui';

    private const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * @return array<int, array{id: string, nama: string, kategori: string, penanggung_jawab: string,
     *     tanggal: string, tanggal_iso: string, tahun: int, status: string, semester: string}>
     */
    public static function semua(): array
    {
        $daftar = [
            ['Workshop Penyusunan Kurikulum OBE', 'Pelatihan', 'Kaprodi TRK', '2026-01-01'],
            ['Rapat Kerja Program Studi', 'Seminar', 'Kaprodi TRK', '2026-02-05'],
            ['Studi Banding Prodi ke Politeknik Mitra', 'Kunjungan Industri', 'Sekretaris Prodi', '2026-03-09'],
            ['Pelatihan Penjaminan Mutu Internal', 'Pelatihan', 'Gugus Penjaminan Mutu', '2026-04-13'],
            ['Kunjungan Industri Mitra Prodi', 'Kunjungan Industri', 'Koordinator Kerja Sama', '2026-05-17'],
            ['Seminar Pemetaan Kompetensi Lulusan', 'Seminar', 'Kaprodi TRK', '2026-06-21'],
        ];

        $rows = [];
        foreach ($daftar as $i => [$nama, $kategori, $pj, $iso]) {
            $rows[] = [
                'id' => (string) ($i + 1),
                'nama' => $nama,
                'kategori' => $kategori,
                'penanggung_jawab' => $pj,
                'tanggal' => self::format($iso),
                'tanggal_iso' => $iso,
                'tahun' => (int) substr($iso, 0, 4),
                'status' => self::DISETUJUI,
                'semester' => $i % 2 === 0 ? 'Ganjil' : 'Genap',
            ];
        }

        return $rows;
    }

    /** Daftar tahun yang ada di data, urut naik (tahun terbaru di ujung). */
    public static function tahun(array $rows): array
    {
        $tahun = array_values(array_unique(array_map(fn ($r) => (int) $r['tahun'], $rows)));
        sort($tahun);

        return $tahun;
    }

    private static function format(string $iso): string
    {
        [$t, $b, $h] = array_map('intval', explode('-', $iso));

        return $h.' '.self::BULAN[$b].' '.$t;
    }
}
