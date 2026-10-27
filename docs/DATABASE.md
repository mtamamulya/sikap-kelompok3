# Dokumentasi Database — SIKAP Kelompok 3

> **Status: MENUNGGU ERD FINAL.**
> Migration akan dibuat setelah ERD resolusi tinggi diterima dan diverifikasi.

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

> Diisi setelah ERD dibaca. Format per tabel:

<!--
### nama_tabel

Menyimpan ....

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id_x  | bigint unsigned AI | tidak | PK |

**Relasi:**
- `belongsTo` ... via ...
-->

---

## Urutan migration

Urutan penting karena foreign key. Tabel induk harus dibuat lebih dulu.

> Diisi setelah ERD dibaca.

---

## Cara menjalankan ulang dari nol

```bash
php artisan migrate:fresh --seed
```

Perintah ini **menghapus semua data**. Jangan dijalankan di server produksi.
