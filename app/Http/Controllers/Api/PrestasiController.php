<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;

class PrestasiController extends Controller
{
    public function index()
    {
        $prestasi = Prestasi::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar prestasi berhasil diambil',
            'data' => $prestasi->items(),
            'meta' => [
                'current_page' => $prestasi->currentPage(),
                'per_page' => $prestasi->perPage(),
                'total' => $prestasi->total(),
                'last_page' => $prestasi->lastPage(),
            ],
        ], 200);
    }
}
