<?php

namespace Database\Seeders;

use App\Repositories\PenempatanKelasMahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiwayatAkademikSeeder extends Seeder
{
    public function run(PenempatanKelasMahasiswa $penempatan): void
    {
        // 1. Daftar tahun akademik masa lalu yang ingin di-generate riwayat kelasnya
        $tahunAkademikMasaLalu = [
            'Ganjil 2023/2024',
            'Genap 2023/2024',
            'Ganjil 2024/2025',
            'Genap 2024/2025',
            'Ganjil 2025/2026',
            'Genap 2025/2026',
        ];

        foreach ($tahunAkademikMasaLalu as $ta_nama) {
            // 2. Ambil data tahun akademik dari database (sudah diinput rapi oleh DataMasterSeeder)
            $ta = DB::table('tahun_akademik')->where('tahun_akademik', $ta_nama)->first();

            // 3. Eksekusi penempatan kelas seolah-olah kita berada di tahun akademik tersebut
            if ($ta) {
                $hasil = $penempatan->buatUntukSemester((int) $ta->id_tahun_akademik);

                // Tampilkan log di terminal Laragon
                $pesan = "Riwayat {$ta_nama}: {$hasil['dibuat']} dibuat, {$hasil['sudah-ada']} sudah ada, {$hasil['belum-mulai']} belum masuk.";
                $this->command->info($pesan);
            } else {
                // Peringatan jika DataMasterSeeder belum dijalankan atau ada typo
                $this->command->warn("Tahun akademik {$ta_nama} tidak ditemukan di database!");
            }
        }
    }
}
