<?php

namespace Database\Seeders;

use App\Models\JenisDokumen;
use Illuminate\Database\Seeder;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'nama_jenis_dokumen' => 'Sertifikat',
                'keterangan' => 'Sertifikat keikutsertaan atau penghargaan kegiatan',
            ],
            [
                'nama_jenis_dokumen' => 'Surat Tugas',
                'keterangan' => 'Surat tugas resmi dari institusi',
            ],
            [
                'nama_jenis_dokumen' => 'Laporan Kegiatan',
                'keterangan' => 'Laporan hasil pelaksanaan kegiatan',
            ],
            [
                'nama_jenis_dokumen' => 'Foto Kegiatan',
                'keterangan' => 'Dokumentasi foto pelaksanaan kegiatan',
            ],
            [
                'nama_jenis_dokumen' => 'Surat Keterangan',
                'keterangan' => 'Surat keterangan pendukung kegiatan',
            ],
        ];

        foreach ($items as $item) {
            JenisDokumen::updateOrCreate(
                ['nama_jenis_dokumen' => $item['nama_jenis_dokumen']],
                ['keterangan' => $item['keterangan']]
            );
        }
    }
}
