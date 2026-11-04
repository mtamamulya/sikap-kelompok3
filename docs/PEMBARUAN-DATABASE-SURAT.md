# Pembaruan surat, riwayat status, dan akreditasi

Realisasi pembaruan database yang disampaikan 2 November 2026.
Migration baru bertanggal 3 November 2026; migration yang sudah di-merge
tidak diubah. Semua nama kolom mengikuti snake_case huruf kecil.

| Tabel | Kolom | Tipe / default |
|---|---|---|
| kategori | template_surat | TEXT, nullable |
| kategori | lampiran_peserta | boolean, default false |
| users | tanda_tangan_path | VARCHAR(255), nullable |
| dokumen | is_lampiran | boolean, default false |
| akreditasi | tahun_terbit | YEAR, nullable |
| akreditasi | berlaku_sampai | DATE, nullable |
| akreditasi | status | boolean, nullable |

Status akreditasi **disepakati aktif/nonaktif**, konsisten dengan boolean
status pada users/kurikulum. `true` berarti aktif, `false` nonaktif; `null`
berarti belum ditentukan. Nilai status dan tanggal tidak ditebak untuk
record lama. Status tidak otomatis dihitung dari tanggal karena aturan
bisnis tersebut belum diberikan. Status ini berbeda dari peringkat
akreditasi yang disimpan pada kolom keterangan.

Relasi database kegiatan–timeline sudah 1:N: `timeline.id_kegiatan` bukan
UNIQUE. Model dilengkapi `Kegiatan::timeline()` (HasMany) dan
`Timeline::kegiatan()` (BelongsTo). `statusTerkini()` tetap membaca satu
baris terbaru melalui tanggal_status; ini tidak mengubah kardinalitas
riwayat. Setiap transisi harus membuat record Timeline baru melalui
controller/service; pembaruan ini belum menambahkan endpoint transisi.

Tanda tangan ditujukan untuk Kaprodi; pembatasan siapa yang dapat mengisi
path dan memilih lampiran harus diterapkan pada Policy/validasi endpoint.
Migration tidak memvalidasi role. Path hanya menyimpan lokasi file, bukan
mengunggah berkas. template_surat menyimpan teks template; belum ada
generator surat atau pemrosesan placeholder.

Untuk memperbarui database setelah ditinjau, jalankan `php artisan migrate`.
Jangan memakai migrate:fresh karena menghapus data. Rollback migration
baru menghapus kolom tambahan beserta isinya, tetapi mempertahankan tabel
dan kolom lama. Pengujian otomatis menggunakan SQLite in-memory, bukan
database lokal pengguna.
