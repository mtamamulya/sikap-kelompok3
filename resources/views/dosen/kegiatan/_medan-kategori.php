<?php

/*
 * Isian tambahan form Kegiatan menurut kategori. Dipakai create.blade.php dan edit.blade.php.
 * Mau mengubah / menambah isian? Cukup edit file ini, tanpa menyentuh halaman form.
 *
 * Per kategori:
 *   'penyelenggara' => label + contoh isian untuk kolom "Penyelenggara" (bisa berubah, mis. "Nama perusahaan")
 *   'medan'         => daftar isian tambahan. Tiap isian:
 *       'nama'  = nama field yang dikirim ke backend (detail[nama])
 *       'label' = tulisan label
 *       'tipe'  = 'pilihan' (dropdown) | 'radio' | 'teks' | 'angka'
 *       'opsi'  = daftar pilihan (untuk 'pilihan' dan 'radio')
 *       'wajib' = true / false
 *       'catatan' = (opsional) tulisan kecil di bawah isian
 */
return [
    'Lomba' => [
        'penyelenggara' => ['label' => 'Penyelenggara lomba', 'placeholder' => 'Nama penyelenggara lomba'],
        'medan' => [
            ['nama' => 'tingkat', 'label' => 'Tingkat', 'tipe' => 'pilihan', 'wajib' => true,
             'opsi' => ['Internal', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional']],
            ['nama' => 'bentuk', 'label' => 'Bentuk keikutsertaan', 'tipe' => 'radio', 'wajib' => true,
             'opsi' => ['Individu', 'Tim'], 'catatan' => 'Pilih Tim untuk menambahkan anggota kegiatan.'],
            ['nama' => 'capaian', 'label' => 'Capaian', 'tipe' => 'pilihan', 'wajib' => true,
             'opsi' => ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan', 'Finalis', 'Peserta']],
        ],
    ],

    'Kunjungan Industri' => [
        'penyelenggara' => ['label' => 'Nama industri tujuan', 'placeholder' => 'PT Contoh Industri'],
        'medan' => [
            ['nama' => 'lokasi', 'label' => 'Kota / lokasi', 'tipe' => 'teks', 'wajib' => true, 'placeholder' => 'Contoh : Bandung'],
            ['nama' => 'jumlah_peserta', 'label' => 'Jumlah peserta', 'tipe' => 'angka', 'wajib' => true, 'placeholder' => 'Contoh : 40'],
        ],
    ],

    'Seminar' => [
        'penyelenggara' => ['label' => 'Penyelenggara', 'placeholder' => 'Nama institusi'],
        'medan' => [
            ['nama' => 'peran', 'label' => 'Peran', 'tipe' => 'pilihan', 'wajib' => true,
             'opsi' => ['Peserta', 'Pembicara', 'Panitia']],
            ['nama' => 'tingkat', 'label' => 'Tingkat', 'tipe' => 'pilihan', 'wajib' => true,
             'opsi' => ['Lokal', 'Nasional', 'Internasional']],
            ['nama' => 'mode', 'label' => 'Mode pelaksanaan', 'tipe' => 'radio', 'wajib' => true,
             'opsi' => ['Luring', 'Daring']],
        ],
    ],

    'Pelatihan' => [
        'penyelenggara' => ['label' => 'Penyelenggara', 'placeholder' => 'Nama institusi'],
        'medan' => [
            ['nama' => 'jumlah_jam', 'label' => 'Jumlah jam pelatihan', 'tipe' => 'angka', 'wajib' => true, 'placeholder' => 'Contoh : 24'],
            ['nama' => 'mode', 'label' => 'Mode pelaksanaan', 'tipe' => 'radio', 'wajib' => true,
             'opsi' => ['Luring', 'Daring']],
            ['nama' => 'sertifikat', 'label' => 'Sertifikat', 'tipe' => 'radio', 'wajib' => true,
             'opsi' => ['Ada', 'Tidak ada']],
        ],
    ],
];