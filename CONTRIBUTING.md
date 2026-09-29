# Panduan Kerja Tim — SIKAP Kelompok 3

Dokumen ini bukan formalitas. Kalau lima orang ngoding di repo yang sama tanpa
aturan, yang terjadi adalah konflik merge tiap hari dan kerjaan orang kepatah.
Baca sekali, ikuti seterusnya.

---

## 1. Struktur branch

```
main        ← versi yang sudah jalan & dipresentasikan. Dikunci.
└── develop ← tempat semua fitur berkumpul. Ini branch kerja utama.
    ├── feature/manajemen-kegiatan
    ├── feature/upload-dokumen
    └── fix/validasi-tanggal
```

**Aturan keras:**

- `main` dan `develop` **tidak boleh** di-push langsung. Selalu lewat Pull Request.
- Satu branch = satu pekerjaan. Jangan campur "tambah fitur A" dengan "rapikan CSS".

### Penamaan branch

| Awalan     | Untuk                        | Contoh                          |
|------------|------------------------------|---------------------------------|
| `feature/` | fitur baru                   | `feature/crud-penelitian`       |
| `fix/`     | perbaikan bug                | `fix/error-upload-pdf`          |
| `db/`      | perubahan migration/skema    | `db/tabel-pengabdian`           |
| `docs/`    | dokumentasi saja             | `docs/update-readme`            |

---

## 2. Alur harian

```bash
# 1. Selalu mulai dari develop yang terbaru
git checkout develop
git pull origin develop

# 2. Bikin branch baru
git checkout -b feature/nama-fitur

# 3. Kerjakan, lalu commit
git add .
git commit -m "feat: tambah CRUD data penelitian"

# 4. Push
git push -u origin feature/nama-fitur
```

Lalu buka Pull Request di GitHub: `feature/nama-fitur` → `develop`.

### Kalau develop sudah maju duluan

```bash
git checkout develop
git pull origin develop
git checkout feature/nama-fitur
git merge develop          # selesaikan konflik kalau ada
```

---

## 3. Format pesan commit

Pakai [Conventional Commits](https://www.conventionalcommits.org/). Formatnya:

```
<tipe>: <deskripsi singkat pakai huruf kecil>
```

| Tipe       | Kapan dipakai                          |
|------------|----------------------------------------|
| `feat`     | menambah fitur                         |
| `fix`      | memperbaiki bug                        |
| `db`       | migration / perubahan skema database   |
| `docs`     | dokumentasi                            |
| `style`    | format kode, tidak mengubah logika     |
| `refactor` | rombak kode tanpa mengubah perilaku    |
| `test`     | menambah atau memperbaiki test         |
| `chore`    | konfigurasi, dependency, hal teknis    |

**Contoh bagus:**

```
feat: tambah endpoint daftar kegiatan prodi
fix: perbaiki validasi tanggal pengabdian yang tertukar
db: tambah tabel dokumen_pendukung beserta relasinya
```

**Contoh jelek:**

```
update
fix bug
asdasd
revisi terakhir fix banget
```

---

## 4. Aturan khusus migration

Ini paling sering bikin ribut, jadi baca pelan-pelan.

- **Jangan pernah mengubah file migration yang sudah di-merge ke `develop`.**
  Kalau butuh perubahan, buat migration baru (`php artisan make:migration`).
  Alasannya: teman yang sudah menjalankan migration lama tidak akan ikut
  ter-update, dan database kalian jadi beda-beda.

- Kalau terpaksa mengubah migration lama (misalnya masih tahap awal),
  **kabari di grup dulu**, karena semua orang harus jalan
  `php artisan migrate:fresh --seed`.

- Satu PR yang menyentuh migration sebaiknya tidak menyentuh hal lain,
  supaya gampang di-review dan di-revert kalau bermasalah.

---

## 5. File yang TIDAK BOLEH di-commit

Sudah diatur di `.gitignore`, tapi tetap dicek sebelum push:

- `.env` — berisi password database kalian. Jangan pernah masuk repo.
- `vendor/` — hasil `composer install`, ukurannya ratusan MB.
- `node_modules/` — sama, hasil `npm install`.
- `storage/logs/*.log`
- File database lokal (`.sqlite`)

Cek dengan `git status` sebelum `git add .`.

---

## 6. Pull Request

1. Isi template PR yang muncul otomatis
2. Cantumkan nomor issue yang diselesaikan (`Closes #12`)
3. Minta **minimal 1 orang** review
4. Jangan merge PR sendiri tanpa review
5. Setelah di-merge, hapus branch-nya di GitHub

### Sebagai reviewer

Cek hal ini, jangan cuma klik Approve:

- Apakah `php artisan migrate:fresh --seed` masih jalan?
- Apakah ada `dd()`, `var_dump()`, atau `console.log()` yang ketinggalan?
- Apakah ada password/token yang ke-hardcode?
- Apakah nama tabel/kolom konsisten dengan ERD?

---

## 7. Konvensi penamaan di kode

| Hal               | Aturan                    | Contoh                        |
|-------------------|---------------------------|-------------------------------|
| Tabel             | `snake_case`, jamak       | `dokumen_pendukung`           |
| Kolom             | `snake_case`              | `tanggal_mulai`               |
| Primary key       | `id_<nama_tabel_tunggal>` | `id_kegiatan`                 |
| Foreign key       | sama dengan PK induknya   | `id_dosen`                    |
| Model             | `PascalCase`, tunggal     | `DokumenPendukung`            |
| Controller        | `PascalCase` + Controller | `KegiatanController`          |

Bahasa Indonesia untuk nama tabel/kolom (mengikuti ERD), bahasa Inggris untuk
istilah framework (`index`, `store`, `update`, `destroy`).

---

## 8. Kalau bingung atau stuck

Jangan diam berhari-hari. Buka issue di GitHub atau tanya di grup. Lebih baik
bertanya 10 menit daripada salah arah 3 hari.
