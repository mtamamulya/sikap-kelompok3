# Implementasi ERD revisi 4 November 2026

Sumber implementasi: section **ini Tama btw**, node `2461:19905` pada board `Yceig3sspt0QDi7G3HEZ2p`. Dibaca ulang melalui browser pada 4 November karena konektor Figma terkena batas penggunaan. Ini mengikuti section pengguna, bukan board usulan 36 tabel yang dibuat sebelumnya.

## Struktur kegiatan

- `kategori` mempunyai banyak `kegiatan` dan `isian_kategori`.
- `isian_kategori`: `id_isian_kategori`, `id_kategori`, `input`, `jenis_input`, `aturan`, `jumlah_isian`. NULL pada jumlah berarti boleh banyak tanpa batas tetap; angka positif membatasi jumlah jawaban. Aturan wajib dicek saat pengiriman, bukan menghalangi draft kosong.
- `kegiatan` mempunyai banyak `detail_kegiatan`; setiap detail menyimpan `id_detail_kegiatan`, `id_kegiatan`, `id_isian_kategori`, `isian`.
- `kegiatan` terhubung ke pengaju melalui `id_user`, dan ke peserta melalui `anggota_kegiatan`.
- `kegiatan` mempunyai banyak prestasi, berita, dokumen, dan timeline. `bukti_prestasi` terkait prestasi dan jenis dokumen, dengan link serta nama berkas.
- Data skim/kelompok bidang yang dipilih dipindah menjadi jawaban `skim`/`kelompok_bidang`. Section terbaru tidak memiliki tabel OPSI_ISIAN, sehingga tabel itu tidak ditambahkan. Penyediaan daftar dropdown administratif masih perlu kontrak terpisah; ini implementasi struktur database/model, bukan backend form lengkap.

## Keputusan saat garis dan kolom berbeda

1. Ada garis DETAIL_KEGIATAN ke DOKUMEN tetapi FK belum tercantum pada kotak dokumen. Ditambahkan `dokumen.id_detail_kegiatan` nullable. Dokumen umum tetap dapat terkait langsung ke kegiatan. Menghapus detail melepaskan tautan detail, tidak menghapus berkas/dokumen. Model menolak dokumen dari kegiatan lain.
2. PENGAJARAN mencantumkan `id_enrollment_kelas_mk` dan `id_dokumen`. Implementasi mempertahankan kedua FK ini: enrollment dan dokumen masing-masing mempunyai banyak pengajaran. Tidak dibalik hanya untuk mengikuti ujung garis yang bertentangan. Enrollment tingkat kelas mempunyai banyak enrollment kelas mata kuliah.
3. AKREDITASI pada section masih mencantumkan `id_user`, bukan `id_prodi`. Implementasi mengikuti itu; relasi prodi atau tabel SURAT tidak ditambahkan berdasarkan usulan sebelumnya.

## Nama dan kompatibilitas

- Nama tabel fisik `users`/`roles` dan timestamps Laravel dipertahankan. `kategori.template_surat` adalah penyimpanan fisik untuk konsep ISI_SURAT; model menyediakan alias `isi_surat` dua arah tanpa menduplikasi kolom.
- Ditambahkan `kategori.kebutuhan`, `prestasi.capaian`, `berita.sampul`, dan `bukti_prestasi` sesuai section.
- Kolom lama di kegiatan (lokasi, deskripsi, tanggal, kegiatan sebelumnya) serta `prestasi.catatan` tetap disimpan untuk kompatibilitas layar/query yang sudah ada. Nilai umum yang sudah ada juga disalin ke jawaban dinamis saat upgrade. Tidak ada migration fresh/reset atau penghapusan isi database.
- Tabel asal dipertahankan sebagai `legacy_detail_kegiatan`, `legacy_kegiatan_dosen`, `legacy_skim`, `legacy_kelompok_bidang`. Tidak digunakan oleh model aktif; merupakan arsip untuk audit dan pengembalian data asal.
- Model KegiatanDosen, Skim, KelompokBidang dihapus dari domain aktif. Gunakan IsianKategori, DetailKegiatan, dan BuktiPrestasi.
- Rollback mengembalikan tabel asal. Seperti rollback perubahan struktur pada umumnya, data baru yang hanya ada pada struktur dinamis harus dibackup/ditransformasikan sebelum downgrade; jangan menjalankan rollback pada database berisi jawaban baru tanpa rencana pemulihan.

## Integritas dan pemakaian

FK database menjaga keberadaan induk. Penyimpanan Eloquent DetailKegiatan memeriksa kecocokan kategori, jenis angka/tanggal, serta batas jumlah jawaban, dalam transaksi dengan lock induk. Jawaban berkas menggunakan detail kosong yang dihubungkan ke dokumen. Dokumen dan bukti history memeriksa kecocokan kegiatan. Query Builder/bulk SQL melewati event model; endpoint penulisan wajib memakai model/service transaksi dan validasi pengiriman.

Relasi balik akun, dosen/mahasiswa, akademik, kegiatan, timeline, prestasi, dan jenis dokumen dilengkapi. Controller pembaca kegiatan disesuaikan agar tidak lagi menganggap detail/prestasi sebagai satu baris.

Untuk kolom umum yang masih dipakai dashboard lama, endpoint penulisan baru perlu menyelaraskan kolom kompatibilitas dan jawaban dalam transaksi sampai query dashboard dipindahkan sepenuhnya. Pengubahan form/API berada di luar perubahan database/model ini.
