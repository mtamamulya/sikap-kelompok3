<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class KaprodiDashboardController extends Controller
{
    public function index()
    {
        $url = fn (string $nama) => Route::has($nama) ? route($nama) : null;

        // 1. Hitung Dosen Aktif (Wajib punya profil fisik di tabel 'dosen')
        $dosenAktif = User::whereHas('dosen')->where('status', true)->count();

        // 2. Hitung Mahasiswa Aktif (Wajib punya profil fisik di tabel 'mahasiwa')
        $mahasiswaAktif = User::whereHas('mahasiswa')->where('status', true)->count();

        // 3. Hitung Jumlah Kegiatan (Resmi = Disetujui)
        $jumlahKegiatan = Kegiatan::whereHas('statusTerkini', function ($q) {
            $q->where('status', 'Disetujui');
        })->count();

        // 4. Hitung Pengajuan (Menunggu Validasi = Diajukan)
        $menungguValidasi = Kegiatan::whereHas('statusTerkini', function ($q) {
            $q->where('status', 'Diajukan');
        })->count();

        // Sesuaikan route link dengan awalan 'kaprodi.'
        $stats = [
            ['label' => 'Dosen Aktif',       'value' => $dosenAktif, 'url' => $url('kaprodi.dosen')],
            ['label' => 'Mahasiswa Aktif',   'value' => $mahasiswaAktif, 'url' => $url('kaprodi.mahasiswa')],
            ['label' => 'Jumlah Kegiatan',   'value' => $jumlahKegiatan, 'url' => $url('kaprodi.kegiatan.index')],
            ['label' => 'Menunggu Validasi', 'value' => $menungguValidasi, 'url' => $url('kaprodi.pengajuan'), 'red' => $menungguValidasi > 0],
        ];

        // 5. Data Grafik Kegiatan Per Tahun (Hanya yang Disetujui)
        $kegiatanRaw = Kegiatan::whereHas('statusTerkini', function ($q) {
            $q->where('status', 'Disetujui');
        })
            ->selectRaw('YEAR(tanggal_mulai) as tahun, count(*) as jumlah')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->pluck('jumlah', 'tahun')
            ->toArray();

        $kegiatanPerTahun = empty($kegiatanRaw) ? [date('Y') => 0] : $kegiatanRaw;

        // 6. Data Tabel 5 Pengajuan Terbaru
        $pengajuanTerbaru = Kegiatan::with(['dosen', 'mahasiswa', 'statusTerkini'])
            ->whereHas('statusTerkini', function ($q) {
                $q->where('status', 'Diajukan');
            })
            ->latest('created_at')
            ->limit(5)
            ->get();

        Carbon::setLocale('id'); // Bahasa Indonesia untuk format waktu
        $rows = $pengajuanTerbaru->map(function ($item) {
            $pengaju = $item->dosen->nama ?? $item->mahasiswa->nama ?? 'Tanpa Nama';
            $namaKegiatan = $item->nama_kegiatan ?? $item->nama ?? 'Tanpa Judul';

            return [
                'id' => $item->id_kegiatan ?? $item->id,
                'kegiatan' => $namaKegiatan,
                'dosen' => $pengaju,
                'tanggal' => Carbon::parse($item->created_at)->diffForHumans(),
                'raw_date' => $item->created_at,
            ];
        })->toArray();

        return view('kaprodi.dashboard', compact('stats', 'kegiatanPerTahun', 'rows'));
    }

    /** Halaman menu sidebar kaprodi yang isinya belum dibuat. */
    public function placeholder(Request $request)
    {
        $nama = $request->route()->getName();
        $judul = 'Halaman';

        foreach (config('kaprodi.menu') as $m) {
            foreach ($m['children'] ?? [$m] as $item) {
                if (($item['route'] ?? null) === $nama) {
                    $judul = $item['label'];
                }
            }
        }

        return view('kaprodi.placeholder', compact('judul'));
    }
}
