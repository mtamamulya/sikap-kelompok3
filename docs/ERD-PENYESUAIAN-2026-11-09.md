# Penyesuaian ERD dan definisi form 9 November 2026

Sumber: [ERD terbaru, section ini Tama btw](https://www.figma.com/board/Yceig3sspt0QDi7G3HEZ2p/ERD-PBL-TI-BISA?node-id=2461-19905) dan dokumen `mabur duwur kata muklis.docx`. Struktur utama sudah diterapkan oleh migration 4 November; migration baru melengkapi perbedaan yang ditemukan saat membaca ulang sumber.

## Perubahan

- Enum `jenis_kegiatan.nama_jenis_kegiatan` diperbaiki dari `Kegiatan Prodi & Mahasiswa` menjadi `Kegiatan Dosen & Mahasiswa`. ID jenis dan FK kategori tetap sama. Rollback mengembalikan nama lama.
- `FormIsianKategoriSeeder` menyediakan empat jenis kegiatan ERD, kategori Volunteer, Magang, SES, dan enam definisi isian dari Word. Seeder dapat dijalankan ulang tanpa mengganti ID, jawaban, atau pengaturan yang sudah disunting.
- Volunteer: Nama Program Relawan wajib, Komunitas / Organisasi Penyelenggara opsional, Peran / Divisi Relawan opsional; masing-masing satu isian.
- Magang: Posisi / Role Magang wajib, Nama Perusahaan / Instansi opsional, dokumentasi opsional dengan `jumlah_isian = NULL` agar dapat berulang.
- Kolom aturan yang kosong di Word diperlakukan opsional. Jenis dokumentasi ditafsirkan sebagai `berkas`. SES hanya memiliki kategori karena Word belum mendefinisikan isian SES.
- Tidak memasukkan contoh nama orang atau jawaban di Word sebagai kegiatan nyata.

## Pemetaan sistem Word

`kategori` menyimpan jenis form. `isian_kategori` menyimpan nama input, jenis, aturan wajib (`NOT NULL`) atau opsional (`NULL`), dan batas jumlah jawaban. `kegiatan` menyimpan induk pengajuan; `detail_kegiatan` menyimpan jawaban dan menghubungkannya ke definisi input. Jawaban berulang disimpan dalam beberapa baris detail, bukan penambahan kolom atau teks daftar dalam satu kolom. Dokumen dapat ditautkan ke detail melalui `dokumen.id_detail_kegiatan`.

Penempatan jawaban mengikuti FK pada ERD Figma; sketsa Word dipakai sebagai contoh perilaku form, bukan untuk memindahkan jawaban ke tabel induk kegiatan. Nomor dan tanggal SK tetap berada di `kegiatan` sesuai catatan ERD.

## Kompatibilitas yang dipertahankan

Keputusan pada [implementasi 4 November](ERD-REVISI-2026-11-04.md) masih berlaku: `users`/`roles`, timestamps Laravel, alias `isi_surat` ke `template_surat`, `tanda_tangan_path`, boolean status akun, serta nilai `non-akademik` tetap digunakan oleh aplikasi. Tabel `legacy_*` adalah arsip; kolom lama masih dipertahankan untuk query layar yang belum menggunakan form dinamis. Ini kesesuaian relasi dan konsep data, bukan penamaan fisik yang seluruhnya identik dengan gambar.

Aturan wajib disimpan sebagai metadata form. Validasi kelengkapan semua field wajib saat pengiriman serta penggantian form frontend statis dengan form dinamis tetap merupakan pekerjaan integrasi endpoint/UI; perubahan ini menyiapkan database dan model, tanpa mengklaim integrasi tersebut selesai.

## Penerapan

Pastikan koneksi menunjuk database yang dimaksud dan buat backup terlebih dahulu. Jalankan:

```sh
php artisan migrate --force
php artisan db:seed --class=FormIsianKategoriSeeder --force
```

Jangan gunakan `migrate:fresh`, `migrate:reset`, atau seeder akun percobaan pada database yang sudah berisi data. Seeder khusus ini juga dipanggil `DatabaseSeeder` untuk instalasi baru.

## Verifikasi

```sh
php vendor/phpunit/phpunit/phpunit --filter="DynamicActivityErdTest|LatestErdDatabaseTest"
```

Pengujian mencakup upgrade/rollback dengan FK kategori, aturan dan jumlah definisi dari Word, seeding ulang yang mempertahankan penyesuaian administrator, serta relasi dan jawaban berulang dari implementasi dinamis sebelumnya.

Hasil penerapan 9 November 2026: MySQL lokal dan TiDB Cloud `sikap_kelompok3` telah diverifikasi memiliki enum baru, 4 jenis kegiatan, 3 kategori, 6 definisi isian, dan 44 foreign key. Hash baris awal dari 41 tabel diperiksa setelah penerapan; data lama tetap tersedia. Backup SQL dan laporan verifikasi disimpan terpisah di workspace, bukan di repository.

Suite SQLite: 53 tes / 657 assertion lulus. Pengujian ERD pada MySQL sementara: 10 tes / 56 assertion lulus. Perubahan enum juga diuji pada tabel sementara TiDB sebelum penerapan. Pemeriksaan format Laravel Pint lulus.
