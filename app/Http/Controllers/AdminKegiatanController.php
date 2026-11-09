<?php

namespace App\Http\Controllers;

/**
 * Halaman "Kegiatan" (admin): sama dengan versi kaprodi (tab Semua | Dosen | Mahasiswa | Prodi),
 * hanya tampilannya memakai layout admin. Logika ada di KaprodiKegiatanController.
 */
class AdminKegiatanController extends KaprodiKegiatanController
{
    protected string $tampilan = 'admin.kegiatan';
}
