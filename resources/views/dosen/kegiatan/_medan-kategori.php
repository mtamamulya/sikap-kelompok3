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
 *       'tipe'  = 'pilihan' (dropdown) | 'radio' | 'teks' | 'angka' | 'tanggal' | 'rupiah' | 'turunan'
 *                 'rupiah'  = isian nominal, titik ribuan otomatis (backend membuang titiknya)
 *                 'turunan' = terisi otomatis dari isian lain, tidak bisa diketik. Butuh 'dari' (nama isian sumber)
 *                             dan 'peta' (nilai sumber => nilai yang ditampilkan)
 *       'opsi'  = daftar pilihan (untuk 'pilihan' dan 'radio')
 *       'wajib' = true / false
 *       'catatan' = (opsional) tulisan kecil di bawah isian
 */

// Isian yang sama untuk Penelitian dan Pengabdian (diambil dari halaman create lama keduanya).
// Skim, peta ranah, dan daftar sebelumnya berbeda per kategori, jadi dilewatkan lewat fungsi di bawah.
$bidang = ['Teknologi Informasi', 'Energi', 'Pendidikan', 'Kesehatan', 'Pertanian'];

$isianRisetPengabdian = fn (array $skim, array $sebelumnya, string $sebutan) => [
    'penyelenggara' => ['label' => 'Afiliasi', 'placeholder' => 'Program Studi / Jurusan / Perguruan Tinggi'],
    'medan' => [
        ['nama' => 'skim', 'label' => 'Skim', 'tipe' => 'pilihan', 'wajib' => true, 'opsi' => array_keys($skim)],
        ['nama' => 'ranah', 'label' => 'Ranah', 'tipe' => 'turunan', 'dari' => 'skim', 'peta' => $skim,
            'placeholder' => 'Terisi otomatis dari skim'],
        ['nama' => 'kelompok_bidang', 'label' => 'Kelompok bidang', 'tipe' => 'pilihan', 'wajib' => true, 'opsi' => $bidang],
        ['nama' => 'sebelumnya', 'label' => $sebutan.' sebelumnya (opsional)', 'tipe' => 'pilihan', 'wajib' => false, 'opsi' => $sebelumnya],
        ['nama' => 'tahun_pelaksanaan_ke', 'label' => 'Tahun pelaksanaan ke-', 'tipe' => 'angka', 'wajib' => true, 'placeholder' => 'Contoh : 1'],
        ['nama' => 'lama_kegiatan', 'label' => 'Lama kegiatan (tahun)', 'tipe' => 'angka', 'wajib' => true, 'placeholder' => 'Contoh : 3'],
        ['nama' => 'lokasi', 'label' => 'Lokasi', 'tipe' => 'teks', 'wajib' => true, 'placeholder' => 'Contoh : Kota Semarang'],
        ['nama' => 'mitra', 'label' => 'Mitra (opsional)', 'tipe' => 'teks', 'wajib' => false, 'placeholder' => 'Nama mitra kerja sama'],
        ['nama' => 'nomor_sk', 'label' => 'Nomor SK penugasan', 'tipe' => 'teks', 'wajib' => true, 'placeholder' => 'Nomor SK'],
        ['nama' => 'tanggal_sk', 'label' => 'Tanggal SK penugasan', 'tipe' => 'tanggal', 'wajib' => true],
        ['nama' => 'dana_dikti', 'label' => 'Dana DIKTI', 'tipe' => 'rupiah', 'wajib' => false],
        ['nama' => 'dana_perguruan_tinggi', 'label' => 'Dana perguruan tinggi', 'tipe' => 'rupiah', 'wajib' => false],
        ['nama' => 'dana_institusi_lain', 'label' => 'Dana institusi lain', 'tipe' => 'rupiah', 'wajib' => false],
        ['nama' => 'in_kind', 'label' => 'In kind', 'tipe' => 'rupiah', 'wajib' => false],
    ],
];

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

    'Penelitian' => $isianRisetPengabdian(
        [
            'Penelitian Dasar Unggulan Perguruan Tinggi' => 'Penelitian Dasar',
            'Penelitian Terapan' => 'Penelitian Terapan',
            'Penelitian Pengembangan' => 'Penelitian Pengembangan',
        ],
        // DATA TIRUAN: nanti diganti judul penelitian milik dosen dari database
        ['Deteksi hoaks dengan ML (2025)', 'Analisis sentimen ulasan aplikasi kampus (2024)'],
        'Penelitian',
    ),

    'Pengabdian' => $isianRisetPengabdian(
        [
            'Pengabdian Kepada Masyarakat Unggulan Perguruan Tinggi' => 'Pengabdian Masyarakat',
            'Pengabdian Kemitraan Masyarakat' => 'Pengabdian Kemitraan',
            'Pengabdian Pemberdayaan Berbasis Kewilayahan' => 'Pengabdian Pemberdayaan',
        ],
        // DATA TIRUAN: nanti diganti judul pengabdian milik dosen dari database
        ['Pelatihan Industri (2025)', 'Seminar Nasional AI (2025)'],
        'Pengabdian',
    ),
];
