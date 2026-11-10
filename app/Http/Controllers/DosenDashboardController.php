<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class DosenDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Kegiatan::untukUser($request->user());
        $stats = [];

        foreach (['Pengajaran' => 'pengajaran.index', 'Penelitian' => 'penelitian.index', 'Pengabdian' => 'pengabdian.index'] as $label => $route) {
            $stats[] = [
                'label' => $label,
                'value' => (clone $query)->whereHas('kategori', fn ($category) => $category->where('nama_kategori', 'like', $label.'%'))->count(),
                'aria'  => 'Buka '.mb_strtolower($label),
                'alert' => false,
                'url'   => route($route),
            ];
        }

        // Ambil kegiatan yang memiliki status terkini
        $waiting = (clone $query)->has('statusTerkini');

        $stats[] = [
            'label' => 'Menunggu Verifikasi',
            'value' => (clone $waiting)->count(),
            'aria'  => 'Buka kegiatan menunggu verifikasi',
            'alert' => true,
            'url'   => route('dosen.kegiatan.index'),
        ];

        $perTahun = [];
        foreach (range(now()->year - 4, now()->year + 2) as $year) {
            $perTahun[$year] = (clone $query)->whereYear('tanggal_mulai', $year)->count();
        }

        $menunggu = $waiting->with('kategori')->latest('created_at')->limit(5)->get()->map(fn ($item) => [
            'id'      => $item->getKey(),
            'nama'    => $item->nama_kegiatan,
            'jenis'   => $item->kategori?->nama_kategori ?? '—',
            'tanggal' => $item->created_at?->locale('id')->translatedFormat('d F Y') ?? '—',
        ]);

        return view('dosen.dashboard', compact('stats', 'perTahun', 'menunggu'));
    }
}