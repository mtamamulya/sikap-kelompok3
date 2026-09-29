# SIKAP — Kelompok 3

**Sistem Informasi Kegiatan dan Arsip Prodi**
Politeknik Negeri Semarang — PBL TI-2B · `PBL-202402-02`

Backend bagian Kelompok 3. Sistem ini mengelola data Tri Dharma, kegiatan
program studi, dan repositori dokumen secara terpusat.

---

## Arsitektur singkat

Proyek SIKAP dibangun oleh dua tim dengan **dua database terpisah** yang
berkomunikasi lewat HTTP API:

```
┌─────────────────────┐          ┌─────────────────────┐
│  Kelompok 1         │  HTTP    │  Kelompok 3         │
│  (repo & DB sendiri)│ ◄──────► │  (repo ini)         │
│                     │   API    │                     │
│  db: sikap_k1       │          │  db: sikap_kelompok3│
└─────────────────────┘          └─────────────────────┘
```

Tidak ada `JOIN` lintas database. Semua pertukaran data lewat endpoint yang
disepakati — lihat [`docs/API-CONTRACT.md`](docs/API-CONTRACT.md).

## Stack

| Komponen  | Versi        |
|-----------|--------------|
| PHP       | 8.2          |
| Laravel   | 12.x         |
| MySQL     | 8.0          |
| Node.js   | 20+ (Vite)   |

---

## Cara menjalankan di komputer sendiri

### 1. Prasyarat

Pastikan sudah terpasang: PHP 8.2+, Composer, MySQL 8.0, Node.js 20+, Git.
Pakai **Laragon** atau **XAMPP** juga boleh, asal versi PHP-nya 8.2 ke atas.

Cek dulu:

```bash
php -v         # harus 8.2.x atau lebih
composer -V
mysql --version
node -v
```

### 2. Clone & install

```bash
git clone https://github.com/<owner>/sikap-kelompok3.git
cd sikap-kelompok3

composer install
npm install
```

### 3. Konfigurasi

```bash
cp .env.example .env      # Windows: copy .env.example .env
php artisan key:generate
```

Buka `.env`, sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan MySQL lokalmu.

### 4. Siapkan database

```sql
CREATE DATABASE sikap_kelompok3
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Lalu:

```bash
php artisan migrate --seed
```

### 5. Jalankan

```bash
php artisan serve     # http://localhost:8000
npm run dev           # terminal terpisah
```

Cek API hidup: buka `http://localhost:8000/api/v1/ping`

---

## Struktur folder yang sering disentuh

```
app/
  Http/Controllers/     ← controller
  Models/               ← model Eloquent
database/
  migrations/           ← struktur tabel (hasil realisasi ERD)
  seeders/              ← data awal
routes/
  api.php               ← endpoint API (prefix /api/v1)
  web.php               ← halaman web
docs/
  DATABASE.md           ← dokumentasi skema & relasi
  API-CONTRACT.md       ← kontrak API dengan Kelompok 1
```

---

## Alur kerja tim

Baca [`CONTRIBUTING.md`](CONTRIBUTING.md) sebelum mulai ngoding. Ringkasnya:

1. Jangan pernah commit langsung ke `main` atau `develop`
2. Bikin branch sendiri: `feature/nama-fitur`
3. Push, lalu buka Pull Request ke `develop`
4. Minta minimal 1 orang review sebelum merge

---

## Tim — Kelompok 3

| Nama                        | Peran                |
|-----------------------------|----------------------|
| Mukhlish Pratama Mulya      | Backend & Database   |
| Inayah Cikal Nurshabrina    | UI/UX & Frontend     |
| Lukman Erif Wicaksono       | Backend              |
| Muhammad Rhafi Hairy Muslim | Backend              |
| Rahma Aprilliana            | Database             |
| Rasya Pandu Wicaksono       | Database             |
| Revo Setyo Kinasih          | Backend              |
| Rita Yulia Sari             | UI/UX & Frontend     |

**Manajer Proyek:** Wiktasari, S.T., M.Kom.
