<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar berita berhasil diambil',
            'data'    => $berita->items(),
            'meta'    => [
                'current_page' => $berita->currentPage(),
                'per_page'     => $berita->perPage(),
                'total'        => $berita->total(),
                'last_page'    => $berita->lastPage(),
            ]
        ], 200);
    }
}
