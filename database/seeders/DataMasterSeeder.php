<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Isi Tabel Prodi
        DB::table('prodi')->updateOrInsert(
            ['kode_prodi' => 'TRK'],
            ['nama_prodi' => 'Teknologi Rekayasa Komputer', 'jenjang' => 'D4']
        );

        // 2. Isi Tabel Kelas (A, B, C, D)
        foreach (['A', 'B', 'C', 'D'] as $k) {
            DB::table('kelas')->updateOrInsert(
                ['jenis_kelas' => $k],
                ['keterangan' => "Kelas $k"]
            );
        }

        // 3. Isi Tabel Tingkat (1, 2, 3, 4)
        foreach ([1, 2, 3, 4] as $t) {
            DB::table('tingkat')->updateOrInsert(
                ['tingkat' => $t],
                ['keterangan' => "Tingkat $t"]
            );
        }

        // 4. Pastikan role 'mahasiswa' ada (dibutuhkan oleh MahasiswaDbStore)
        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'mahasiswa'],
            ['nama_role' => 'mahasiswa']
        );

        // 5. Isi Tabel Tahun Akademik SECARA KRONOLOGIS
        // Diurutkan dari masa lalu ke masa depan agar ID di database urut dan rapi
        $tahunAkademik = [
            ['tahun_akademik' => 'Ganjil 2023/2024', 'status' => 0],
            ['tahun_akademik' => 'Genap 2023/2024', 'status' => 0],
            ['tahun_akademik' => 'Ganjil 2024/2025', 'status' => 0],
            ['tahun_akademik' => 'Genap 2024/2025', 'status' => 0],
            ['tahun_akademik' => 'Ganjil 2025/2026', 'status' => 0],
            ['tahun_akademik' => 'Genap 2025/2026', 'status' => 0],
            ['tahun_akademik' => 'Ganjil 2026/2027', 'status' => 1], // Status 1 = Aktif saat ini
            ['tahun_akademik' => 'Genap 2026/2027', 'status' => 0],
        ];
        
        foreach ($tahunAkademik as $ta) {
            DB::table('tahun_akademik')->updateOrInsert(
                ['tahun_akademik' => $ta['tahun_akademik']],
                ['status' => $ta['status']]
            );
        }
    }
}