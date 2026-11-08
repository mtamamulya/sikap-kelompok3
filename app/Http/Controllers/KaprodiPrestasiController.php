<?php

namespace App\Http\Controllers;

use App\Models\Prestasi; // Wajib dipanggil agar bisa query ke DB
use Illuminate\Http\Request;

class KaprodiPrestasiController extends Controller
{
    public function index()
    {
        return view('kaprodi.prestasi', ['rows' => $this->daftar()]);
    }

    public function show($id)
    {
        $rows = $this->daftar();
        $prestasi = collect($rows)->firstWhere('id', (int) $id);

        if (! $prestasi) {
            abort(404);
        }

        // Kita juga kirim model aslinya ke view jika detail butuh data relasi Bukti Prestasi
        $modelAsli = Prestasi::with(['kegiatan', 'buktiPrestasi.jenisDokumen'])->find($id);

        return view('kaprodi.prestasi-detail', compact('prestasi', 'modelAsli'));
    }

    private function daftar(): array
    {
        // Tarik data Prestasi beserta relasi Kegiatan dan User dari database
        $prestasiDb = Prestasi::with(['kegiatan.user', 'kegiatan.kategori'])->get();

        $rows = [];
        foreach ($prestasiDb as $p) {
            $keg = $p->kegiatan;
            $usr = $keg ? $keg->user : null;

            // 1. Tentukan Nama Pengunggah
            $namaPengunggah = $usr ? ($usr->nama ?? $usr->name ?? 'User Tidak Diketahui') : 'Sistem';

            // 2. Tentukan Tipe (Dosen / Mahasiswa)
            $tipeUser = 'Mahasiswa'; 
            if ($usr) {
                // CATATAN UNTUKMU: Sesuaikan kata 'role' di bawah dengan nama kolom peran di tabel usermu
                // Misalnya: $usr->jenis_pengguna === 'Dosen'
                if (isset($usr->role) && strtolower($usr->role) === 'dosen') {
                    $tipeUser = 'Dosen';
                } elseif (isset($usr->is_dosen) && $usr->is_dosen) {
                    $tipeUser = 'Dosen';
                }
            }

            // 3. Tentukan Tanggal & Semester
            $tanggalMulai = $keg && $keg->tanggal_mulai ? $keg->tanggal_mulai : now();
            $bulanAngka = (int) $tanggalMulai->format('n');
            
            // Format string: "21 Mei 2024"
            $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $stringTanggal = $tanggalMulai->format('j') . ' ' . $namaBulan[$bulanAngka] . ' ' . $tanggalMulai->format('Y');
            
            // Logika Akademik: Agustus(8) s/d Januari(1) = Ganjil, sisanya Genap
            $semester = ($bulanAngka >= 8 || $bulanAngka <= 1) ? 'Ganjil' : 'Genap';

            // 4. Masukkan ke array sesuai format Frontend
            $rows[] = [
                'id' => $p->id_prestasi,
                'nama' => $keg ? $keg->nama_kegiatan : 'Tidak Ada Judul',
                'kategori' => $p->jenis_prestasi ?? 'Lomba',
                'pengunggah' => $namaPengunggah,
                'tipe' => $tipeUser,
                'anggota' => [$namaPengunggah], // Default diisi pengunggah dulu
                'tanggal' => $stringTanggal,
                'raw_date' => $tanggalMulai->toDateString(), // Penting untuk sorting DataTables
                'semester' => $semester,
            ];
        }

        return $rows;
    }
}