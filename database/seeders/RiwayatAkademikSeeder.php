<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Repositories\PenempatanKelasMahasiswa;

class RiwayatAkademikSeeder extends Seeder
{
    public function run(PenempatanKelasMahasiswa $penempatan): void
    {
        // 1. Daftar tahun akademik masa lalu yang ingin di-generate
        $tahunAkademikMasaLalu = [
            'Ganjil 2023/2024',
            'Genap 2023/2024',
            'Ganjil 2024/2025',
            'Genap 2024/2025',
            'Ganjil 2025/2026',
            'Genap 2025/2026',
        ];

        foreach ($tahunAkademikMasaLalu as $ta_nama) {
            // 2. Pastikan tahun akademik ini ada di database (status 0 karena sudah lewat)
            DB::table('tahun_akademik')->updateOrInsert(
                ['tahun_akademik' => $ta_nama],
                ['status' => 0]
            );

            // Ambil data tahun akademik yang baru saja dipastikan ada
            $ta = DB::table('tahun_akademik')->where('tahun_akademik', $ta_nama)->first();

            // 3. Eksekusi penempatan kelas seolah-olah kita berada di tahun akademik tersebut
            if ($ta) {
                $hasil = $penempatan->buatUntukSemester((int) $ta->id_tahun_akademik);
                
                // Tampilkan log di terminal Laragon
                $pesan = "Riwayat {$ta_nama}: {$hasil['dibuat']} dibuat, {$hasil['sudah-ada']} sudah ada, {$hasil['belum-mulai']} belum masuk.";
                $this->command->info($pesan);
            }
        }
    }
}