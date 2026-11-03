<?php

namespace App\Http\Controllers;

use App\Repositories\AkreditasiStore;
use Illuminate\Support\Str;

class AkreditasiController extends Controller
{
    /** Halaman publik: menampilkan PDF akreditasi yang sedang aktif (diatur dari halaman admin). */
    public function index(AkreditasiStore $store)
    {
        // TODO-DB: ganti dengan query model Akreditasi (status aktif) saat database dipakai.
        $aktif = collect($store->all())->firstWhere('status', 'aktif');
        $pdfUrl = $aktif ? $store->pdfUrl($aktif) : null;

        return view('akreditasi', [
            'title' => 'Akreditasi',
            'subtitle' => 'Dokumen akreditasi Program Studi Teknologi Rekayasa Komputer Politeknik Negeri Semarang',
            'pdfUrl' => $pdfUrl,
            'fileName' => $aktif
                ? Str::slug($aktif['lembaga'].' '.$aktif['year']).'.pdf'
                : 'akreditasi-trk.pdf',
        ]);
    }
}
