## Apa yang dikerjakan

<!-- Jelaskan singkat perubahan di PR ini -->

## Issue terkait

Closes #

## Jenis perubahan

- [ ] Fitur baru
- [ ] Perbaikan bug
- [ ] Perubahan struktur database (migration)
- [ ] Dokumentasi
- [ ] Refactor / rapi-rapi

## Checklist sebelum minta review

- [ ] `php artisan migrate:fresh --seed` jalan tanpa error
- [ ] `php artisan test` hijau
- [ ] Tidak ada file `.env`, `vendor/`, atau `node_modules/` yang ikut ter-commit
- [ ] Kalau mengubah migration: sudah dikabarkan di grup, karena teman lain perlu `migrate:fresh`
- [ ] Kalau mengubah endpoint di `routes/api.php` yang dipakai Kelompok 1: sudah update `docs/API-CONTRACT.md` dan kabari mereka

## Cara mengetes

1.
2.

## Screenshot (kalau ada perubahan tampilan)
