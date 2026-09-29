# Kontrak API — Kelompok 3 ↔ Kelompok 1

> **Status: DRAFT.** Diisi setelah struktur database final dan kedua tim
> sepakat. Dokumen ini adalah satu-satunya sumber kebenaran soal endpoint;
> kalau ada perubahan di `routes/api.php`, dokumen ini wajib ikut diubah
> di PR yang sama.

---

## Kenapa perlu API

Kelompok 1 dan Kelompok 3 memakai **database yang berbeda**. Artinya:

- Tidak ada `JOIN` lintas tabel antar tim
- Tidak ada foreign key yang menunjuk ke tabel tim lain
- Kalau butuh data tim lain, **panggil API mereka**

Kalau tabel di sisi kita perlu menyimpan referensi ke data Kelompok 1,
simpan **ID-nya saja sebagai kolom biasa** (tanpa constraint FK), misalnya
`id_dosen_eksternal INT UNSIGNED NULL`, lalu ambil detailnya lewat API.

---

## Autentikasi

Memakai **Laravel Sanctum** dengan personal access token.

```
Authorization: Bearer <token>
Accept: application/json
```

Token di-generate sekali oleh masing-masing tim, lalu ditukar secara aman
(jangan kirim lewat chat grup publik, jangan commit ke repo).

Di sisi kita, token Kelompok 1 disimpan di `.env`:

```
KELOMPOK1_API_URL=http://localhost:8001/api/v1
KELOMPOK1_API_TOKEN=xxxxx
```

---

## Format respons standar

Semua endpoint mengembalikan JSON dengan bentuk yang sama.

**Sukses:**

```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": { }
}
```

**Sukses dengan paginasi:**

```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [ ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 42,
    "last_page": 3
  }
}
```

**Gagal:**

```json
{
  "success": false,
  "message": "Data tidak ditemukan",
  "errors": { }
}
```

### Kode status yang dipakai

| Kode | Arti                                      |
|------|-------------------------------------------|
| 200  | Berhasil (GET, PUT, PATCH)                |
| 201  | Berhasil dibuat (POST)                    |
| 204  | Berhasil, tanpa isi (DELETE)              |
| 401  | Token tidak ada atau salah                |
| 403  | Token benar tapi tidak berhak             |
| 404  | Data tidak ditemukan                      |
| 422  | Validasi gagal                            |
| 500  | Error di server                           |

---

## Endpoint yang KITA sediakan

> Diisi setelah ERD final.

| Method | Endpoint                      | Keterangan | Status |
|--------|-------------------------------|------------|--------|
| GET    | `/api/v1/ping`                | Cek service hidup | ✅ ada |
| GET    | `/api/v1/integrasi/...`       | _TBD_      | ⬜ |

---

## Endpoint yang KITA panggil (milik Kelompok 1)

> Diisi setelah Kelompok 1 mengirim kontraknya.

| Method | Endpoint | Keterangan | Status |
|--------|----------|------------|--------|
| GET    | _TBD_    | _TBD_      | ⬜ |

---

## Kesepakatan yang perlu diputuskan bersama

Daftar ini dibawa ke rapat koordinasi dua tim:

1. **Siapa pemilik data user/dosen?** Satu tim jadi sumber kebenaran,
   tim lain hanya menyimpan ID-nya. Menyimpan di dua tempat akan membuat
   data tidak sinkron.
2. **Base URL dan port** masing-masing service saat development.
3. **Format tanggal** — disarankan ISO 8601 (`2026-09-29T17:38:00+07:00`).
4. **Bagaimana kalau service tim lain mati?** Perlu fallback atau cache?
5. **Siapa yang menyimpan file dokumen**, dan bagaimana cara tim lain
   mengaksesnya (URL publik, signed URL, atau streaming lewat API)?
