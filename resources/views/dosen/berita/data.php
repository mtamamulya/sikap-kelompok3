<?php

// DATA TIRUAN pengajuan berita dosen. Dipakai oleh halaman daftar, detail, dan edit
// (resources/views/berita/*). Nanti diganti data dari controller / database oleh tim backend.
// status: Draft | Diajukan | Direvisi | Dipublikasikan | Ditolak
// catatan: catatan revisi (Direvisi) atau alasan penolakan (Ditolak), selain itu null.
$isi = "Isi berita lengkap yang ditulis dosen tampil di sini. Ini hanya contoh teks sementara untuk tampilan.\n\nParagraf berikutnya berisi penjelasan kegiatan, waktu, tempat, dan pihak yang terlibat.";

$daftar = [
    ['judul' => 'Mahasiswa TRK Juara 2 Lomba KMIPN Tingkat Nasional', 'kategori' => 'Prestasi Mahasiswa', 'tanggal' => '12 September 2026', 'status' => 'Diajukan',       'catatan' => null],
    ['judul' => 'Dosen TRK Raih Hibah Penelitian Nasional 2026',      'kategori' => 'Prestasi Dosen',     'tanggal' => '20 Agustus 2026',   'status' => 'Direvisi',       'catatan' => 'Tambahkan nama pemberi hibah dan nominal yang diterima, lalu lampirkan foto yang lebih jelas.'],
    ['judul' => 'Kunjungan Industri ke PT Mitra Teknologi',           'kategori' => 'Kunjungan Industri', 'tanggal' => '3 Agustus 2026',    'status' => 'Dipublikasikan', 'catatan' => null],
    ['judul' => 'Jadwal Pendaftaran Semester Genap 2026/2027',        'kategori' => 'Info Akademik',      'tanggal' => '15 Juli 2026',      'status' => 'Ditolak',        'catatan' => 'Informasi ini sudah diumumkan oleh bagian akademik, jadi tidak perlu dimuat ulang sebagai berita.'],
    ['judul' => 'Lomba Desain Poster Tingkat Provinsi',               'kategori' => 'Lomba',              'tanggal' => '3 Agustus 2026',    'status' => 'Draft',          'catatan' => null],
    ['judul' => 'Pendaftaran Magang MSIB Angkatan Baru Dibuka',       'kategori' => 'Magang',             'tanggal' => '3 Agustus 2026',    'status' => 'Direvisi',       'catatan' => 'Cantumkan batas akhir pendaftaran dan tautan resmi pendaftaran.'],
    ['judul' => 'Seminar Nasional AI Sukses Digelar',                 'kategori' => 'Prestasi Dosen',     'tanggal' => '12 September 2026', 'status' => 'Dipublikasikan', 'catatan' => null],
    ['judul' => 'Dosen TRK Menjadi Narasumber Workshop IoT',          'kategori' => 'Prestasi Dosen',     'tanggal' => '12 September 2026', 'status' => 'Ditolak',        'catatan' => 'Kegiatan belum terverifikasi. Ajukan kembali setelah ada surat tugas.'],
    ['judul' => 'Workshop IoT untuk Siswa SMK Se-Kabupaten',          'kategori' => 'Info Akademik',      'tanggal' => '20 Agustus 2026',   'status' => 'Diajukan',       'catatan' => null],
    ['judul' => 'Kunjungan Industri Mahasiswa ke Bandung',            'kategori' => 'Kunjungan Industri', 'tanggal' => '3 Agustus 2026',    'status' => 'Direvisi',       'catatan' => 'Judul terlalu umum. Sebutkan nama industri yang dikunjungi.'],
];

// id tetap (1, 2, 3, ...) supaya tautan Lihat / Edit tidak berubah saat tabel dicari, difilter, atau diurutkan.
foreach ($daftar as $n => &$d) {
    $d['id']  = $n + 1;
    $d['isi'] = $isi;
}
unset($d);

return $daftar;