<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

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

    public function show(int $id): JsonResponse
    {
        $m = Prestasi::query()->find($id);

        return $m ? ApiResponse::ok(self::bentuk($m)) : ApiResponse::gagal('Prestasi tidak ditemukan');
    }

    /** Nama kunci mengikuti kolom database. Kolom tahun_terbit dan deskripsi bernilai null sampai migration usulan dipasang. */
    public static function bentuk(Prestasi $k): array
    {
        return [
            'id_prestasi' => $k->id_prestasi,
            'id_prodi' => $k->id_prodi,
            'jenis_prestasi' => $k->jenis_prestasi,
            'bidang_lomba' => $k->bidang_lomba,
            'catatan' => $k->getAttribute('catatan'),
            'created_at' => $k->getAttribute('created_at'),
            'updated_at' => $k->getAttribute('updated_at'),
            'capaian' => $k->capaian,
        ];
    }
}
