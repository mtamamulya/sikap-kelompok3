<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Database\Seeder;

class AkademikSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan firstOrCreate agar tidak error jika data sudah ada
        $prodi = Prodi::firstOrCreate(
            ['kode_prodi' => 'TRK'],
            [
                'nama_prodi' => 'Teknologi Rekayasa Komputer',
                'jenjang' => 'D4',
            ]
        );

        $kurikulum = Kurikulum::firstOrCreate(
            ['id_prodi' => $prodi->id_prodi, 'nama_kurikulum' => 'Kurikulum 2024 TRK']
        );

        MataKuliah::firstOrCreate(
            ['id_kurikulum' => $kurikulum->id_kurikulum, 'kode_mk' => 'TRK601'],
            [
                'semester' => 7,
                'nama_mk' => 'Kecerdasan Buatan',
                'sks_teori' => 2,
                'sks_praktik' => 0,
                'jam_teori' => 2,
                'jam_praktek' => 0,
            ]
        );
    }
}
