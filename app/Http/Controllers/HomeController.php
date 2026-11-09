<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;

class HomeController extends Controller
{
    public function index()
    {
        $prestasi = Prestasi::query()
            ->with('kegiatan:id_kegiatan,nama_kegiatan')
            ->latest('id_prestasi')
            ->take(8)
            ->get()
            ->map(function (Prestasi $record) {
                $badge = $record->capaian ?: ucfirst($record->jenis_prestasi);
                $event = $record->kegiatan?->nama_kegiatan;
                $title = collect([$record->capaian ?: $record->catatan, $event])
                    ->filter()
                    ->implode(' - ');

                return [
                    'slug' => null,
                    'badge' => $badge,
                    'color' => match (strtolower($badge)) {
                        'juara 1' => '#FFCA00',
                        'juara 2' => '#949494',
                        'juara 3' => '#AD5C00',
                        default => '#0B3A8C',
                    },
                    'title' => $title ?: $record->bidang_lomba ?: ucfirst($record->jenis_prestasi),
                    'sub' => $record->bidang_lomba ?: ucfirst($record->jenis_prestasi),
                    'img' => null,
                ];
            });

        $counts = Prestasi::query()
            ->selectRaw('jenis_prestasi, COUNT(*) as total')
            ->groupBy('jenis_prestasi')
            ->pluck('total', 'jenis_prestasi');
        $stats = [
            [Prestasi::query()->count(), 'Total prestasi'],
            [$counts->get('akademik', 0), 'Prestasi akademik'],
            [$counts->get('non-akademik', 0), 'Prestasi non-akademik'],
        ];
        // dd($prestasi);

        return view('home', compact('prestasi', 'stats'));
    }
}
