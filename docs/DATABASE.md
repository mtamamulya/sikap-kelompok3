# Dokumentasi Database — SIKAP Kelompok 3

> **Status: ERD dasar sudah dimigrasikan; verifikasi perubahan bersama pemilik
> database sebelum menerapkan migrasi ke cloud.** Source of truth skema lokal
> adalah `database/migrations`.

---

## Prinsip yang dipakai

**Database terpisah dari Kelompok 1.** Tidak ada foreign key yang menunjuk
ke tabel milik tim lain. Referensi lintas sistem disimpan sebagai kolom ID
biasa dan datanya diambil lewat API (lihat `API-CONTRACT.md`).

### Konvensi penamaan

| Hal            | Aturan                       | Contoh                  |
|----------------|------------------------------|-------------------------|
| Nama tabel     | `snake_case`, bahasa Indonesia | `dokumen_pendukung`   |
| Primary key    | `id_<entitas>`               | `id_kegiatan`           |
| Foreign key    | sama persis dengan PK induk  | `id_dosen`              |
| Timestamp      | `created_at`, `updated_at`   | bawaan Laravel          |
| Soft delete    | `deleted_at` bila perlu      |                         |

### Aturan teknis

- Engine: **InnoDB** (wajib, supaya foreign key jalan)
- Charset: `utf8mb4`, collation `utf8mb4_unicode_ci`
- Semua FK memakai `ON DELETE RESTRICT` sebagai default, kecuali tabel
  pivot/detail yang memang ikut terhapus (`ON DELETE CASCADE`)
- Kolom uang/nilai pakai `decimal`, jangan `float`

---

## Daftar tabel

Migrasi yang sudah ada membentuk kelompok tabel berikut:

| Kelompok | Tabel |
|----------|-------|
| Akun | `roles`, `users`, `password_reset_tokens`, `sessions` |
| Referensi akademik | `prodi`, `tahun_akademik`, `tingkat`, `kelas`, `kurikulum`, `mata_kuliah`, `skim`, `kelompok_bidang` |
| Enrolmen | `enrollment_tingkat_kelas`, `enrollment_mahasiswa_tingkat_kelas`, `enrollment_mata_kuliah`, `enrollment_kelas_mata_kuliah` |
| Kegiatan dan status | `jenis_kegiatan`, `kategori`, `kegiatan`, `detail_kegiatan`, `anggota_kegiatan`, `timeline` |
| Arsip dan berita | `jenis_dokumen`, `dokumen`, `bukti_history`, `berita` |
| Dosen dan mahasiswa | `dosen`, `mahasiswa`, `histori_pangkat_golongan`, `pengajaran` |
| Akreditasi | `akreditasi` |
| Keamanan akun | `login_logs`, `otp_codes` |

`timeline` sudah berfungsi sebagai riwayat perubahan status kegiatan. Jangan
membuat tabel `status_histories` duplikat tanpa keputusan perubahan ERD.
Saat ini `timeline` mencatat status, tanggal, dan catatan; atribusi aktor
perubahan status belum tersedia dan perlu disepakati sebelum mengubah skema.

### Alur review pengajuan kegiatan

- Mahasiswa mengajukan kegiatan.
- Admin melakukan review awal. Jika diteruskan, status ERD adalah
  `direview` (di UI saat ini ditampilkan sebagai "Menunggu Tanda Tangan").
- Kaprodi mengambil keputusan akhir: tanda tangan dan menerima (`diterima`),
  meminta revisi (`direvisi`), atau menolak (`ditolak`).
- Setelah revisi dikirim kembali, alur review dimulai lagi dari Admin.
- Status akhir `selesai` digunakan setelah kegiatan yang diterima sudah
  dilaksanakan dan prosesnya ditutup.

Area Admin dan Kaprodi harus tetap terpisah sesuai role akun. Skema `timeline`
belum mencatat ID aktor; jangan menganggap nama role atau aktor tersimpan
dalam riwayat sebelum ada keputusan untuk menambah kolom `id_user`.

`login_logs` mencatat email, akun (bila diketahui), IP, user-agent, hasil,
dan waktu login. Password tidak pernah dicatat. `otp_codes` menyimpan hash
OTP, masa berlaku, jumlah percobaan, waktu pengiriman/verifikasi, dan waktu
konsumsi; OTP berlaku 5 menit, maksimal 3 percobaan, dan jeda kirim ulang
60 detik.

---

## Urutan migration

Urutan ditentukan oleh timestamp nama file. Tabel induk dibuat sebelum tabel
yang memiliki foreign key. Perubahan skema selalu ditambahkan sebagai migration
baru; jangan mengedit migration yang sudah diterapkan ke database bersama.

Sebelum menerapkan migration ke cloud, bandingkan `php artisan migrate:status`
dengan pemilik database dan pastikan backup serta persetujuan tersedia.

---

## Cara menjalankan ulang dari nol

```bash
php artisan migrate:fresh --seed
```

Perintah ini **menghapus semua data**. Jangan dijalankan di server produksi.
Jangan gunakan pada database cloud bersama.
