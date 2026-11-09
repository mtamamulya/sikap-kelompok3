<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;

class PrestasiController extends Controller
{
    public function index()
    {
        $with = [
            'kegiatan:id_kegiatan,nama_kegiatan,tanggal_mulai,tanggal_selesai,lokasi,deskripsi',
            'buktiPrestasi.jenisDokumen:id_jenis_dokumen,nama_jenis_dokumen',
        ];

        $prestasi = Prestasi::query()
            ->with($with)
            ->latest('id_prestasi')
            ->paginate(8);

        $terbaru = Prestasi::query()
            ->with($with)
            ->latest('id_prestasi')
            ->take(8)
            ->get();

        return view('prestasi-index', compact('prestasi', 'terbaru'));
    }

    public function show(int $prestasi)
    {
        $record = Prestasi::query()
            ->with([
                'kegiatan:id_kegiatan,nama_kegiatan,tanggal_mulai,tanggal_selesai,lokasi,deskripsi',
                'buktiPrestasi.jenisDokumen:id_jenis_dokumen,nama_jenis_dokumen',
            ])
            ->findOrFail($prestasi);

        $terkait = Prestasi::query()
            ->with('kegiatan:id_kegiatan,nama_kegiatan')
            ->whereKeyNot($record->getKey())
            ->latest('id_prestasi')
            ->take(4)
            ->get();

        return view('prestasi-show', compact('record', 'terkait'));
    }
}
