<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Prestasi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user?->loadMissing(['mahasiswa', 'dosen', 'role']);
        $profile = $user?->mahasiswa ?? $user?->dosen;
        $nama = $profile?->nama ?? $user?->email ?? 'Belum masuk';
        $role = $user?->role?->nama_role ?? 'Tamu';
        $query = Kegiatan::untukUser($user);
        // Pengajuan adalah kegiatan yang dibuat oleh akun ini, bukan yang hanya diikuti.
        $pengajuan = Kegiatan::where('id_user', $user?->getKey());
        $semesterStart = now()->startOfYear()->month(now()->month >= 8 ? 8 : (now()->month >= 2 ? 2 : 8));
        if (now()->month === 1) {
            $semesterStart->subYear();
        }

        return view('mahasiswa.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'nama' => $nama,
            'role' => $role,
            'pengajuanRoute' => $user?->dosen ? 'pengajuan.dosen' : 'pengajuan.mahasiswa',
            'totalPengajuan' => (clone $pengajuan)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'totalKegiatan' => (clone $query)->count(),
            'totalPrestasi' => Prestasi::whereHas('kegiatan', fn ($q) => $q->untukUser($user)
                ->whereBetween('tanggal_mulai', [$semesterStart, $semesterStart->copy()->addMonths(6)->subDay()]))->count(),
            'pengajuanTerbaru' => $pengajuan->with('statusTerkini')->latest('created_at')->orderByDesc('id_kegiatan')->limit(5)->get(),
        ]);
    }
}
