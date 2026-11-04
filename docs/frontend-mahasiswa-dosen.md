# Frontend mahasiswa dan dosen

Pekerjaan ini merapikan frontend yang sudah ada di SIKAP Kelompok 3 dan menambahkan dashboard dosen dari frontend Naya. Tidak menambahkan login/logout, role Admin/Kaprodi, berita, template surat, atau workflow ALLROLE.

## Halaman utama

- `/dashboard`: dashboard mahasiswa yang sudah menggunakan `DashboardController`, dengan layout mahasiswa. View utama berada di `resources/views/mahasiswa/dashboard.blade.php`; nama view lama hanya menjadi alias kompatibilitas.
- `/kegiatan`: daftar kegiatan mahasiswa yang sudah menggunakan `KegiatanController`.
- `/dosen/dashboard`: dashboard dosen memakai layout Naya dan data kegiatan pada database proyek ini.
- `/dosen/kegiatan`: tampilan daftar kegiatan Naya, menggunakan sumber data dan pembatasan pemilik dari `KegiatanController` yang sudah ada.
- `/penelitian`, `/pengabdian`, `/pengajaran`, `/prestasi`, `/profil`, dan `/notifikasi`: frontend dosen yang sudah tersedia.

Halaman dapat dibuka langsung tanpa login untuk pemeriksaan frontend. Tanpa user dari integrasi autentikasi kelompok 1, dashboard dan daftar kegiatan berbasis database tampil kosong. Tidak memilih akun lain atau mengisi data seolah pengguna sudah login. Detail dan form akademik yang memang masih contoh tetap menjadi frontend contoh; pekerjaan ini tidak menambahkan backend CRUD-nya.

Menu mahasiswa yang belum terhubung ditandai **Belum terhubung**. Tidak dibuatkan fitur baru untuk mengisi kekosongan tersebut. Tautan **Tampilan dosen** dan **Tampilan mahasiswa** memudahkan pemeriksaan kedua layout tanpa login.

## Perapian

- Memisahkan shell mahasiswa dan shell dosen, memperbaiki navigasi HP, ukuran form, fokus keyboard, modal, dan petunjuk scroll tabel.
- Membundel Alpine dari dependensi npm yang sudah ada, sehingga tidak mengandalkan skrip CDN.
- Beberapa nama view lama dijadikan alias agar dashboard/kegiatan tidak mempunyai salinan markup yang berbeda.
- Memperbaiki detail pengajuan dan route verifikasi yang sebelumnya menunjuk variabel/view yang tidak tersedia, menggunakan frontend Naya yang sudah ada. Detail pengajuan tersebut tetap merupakan contoh frontend.
- Mempertahankan perubahan lokal pada model, controller dashboard, dan database. Tidak menambahkan migration atau mengubah `.env`.

## Pemeriksaan

`php artisan test --compact` menjalankan pengujian layout, route tanpa login, data kegiatan milik pengguna, dan pembatasan data pemilik lain menggunakan SQLite in-memory. `npm run build` membangun aset.

Pemeriksaan browser memakai database audit SQLite terpisah; database MySQL `sikap_kelompok3` tidak diubah. Cadangan source sebelum perapian tersimpan di `D:\PBL3\arsip-file-lama\frontend-sebelum-rapih-20261028.zip`.

Perubahan dikerjakan pada branch `fix/rapikan-frontend-mahasiswa-dosen`. Ikuti aturan repository: jika akan dipublikasikan, gunakan PR ke `develop`, bukan push langsung ke `develop` atau `main`.
