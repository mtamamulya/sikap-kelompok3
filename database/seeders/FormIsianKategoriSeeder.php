<?php

namespace Database\Seeders;

use App\Models\JenisKegiatan;
use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormIsianKategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (['Kegiatan Mahasiswa', 'Kegiatan Prodi', 'Kegiatan Dosen & Mahasiswa', 'Kegiatan Dosen'] as $name) {
                JenisKegiatan::firstOrCreate(['nama_jenis_kegiatan' => $name]);
            }

            $kind = JenisKegiatan::where('nama_jenis_kegiatan', 'Kegiatan Mahasiswa')->firstOrFail();
            // Source: "mabur duwur kata muklis.docx". Unspecified rules are
            // optional; SES has no field definitions in the source document.
            $forms = [
                'Volunteer' => [
                    ['Nama Program Relawan', 'teks', 'NOT NULL', 1],
                    ['Komunitas / Organisasi Penyelenggara', 'teks', 'NULL', 1],
                    ['Peran / Divisi Relawan', 'teks', 'NULL', 1],
                ],
                'Magang' => [
                    ['Posisi / Role Magang', 'teks', 'NOT NULL', 1],
                    ['Nama Perusahaan / Instansi', 'teks', 'NULL', 1],
                    ['dokumentasi', 'berkas', 'NULL', null],
                ],
                'SES' => [],
            ];

            foreach ($forms as $name => $fields) {
                $category = Kategori::firstOrCreate([
                    'id_jenis_kegiatan' => $kind->getKey(), 'nama_kategori' => $name,
                ], ['kebutuhan' => 'kegiatan']);

                foreach ($fields as [$input, $type, $rule, $limit]) {
                    // Repeatable without overwriting an administrator's changes
                    // or replacing IDs already referenced by activity answers.
                    $category->isianKategori()->firstOrCreate(['input' => $input], [
                        'jenis_input' => $type, 'aturan' => $rule, 'jumlah_isian' => $limit,
                    ]);
                }
            }
        });
    }
}
