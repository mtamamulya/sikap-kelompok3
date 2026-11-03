<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Support\ApiResponse;
use App\Support\LinkFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** GET /api/v1/akreditasi dan /akreditasi/aktif (baca saja, tanpa token). */
class AkreditasiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['per_page' => ['nullable', 'integer', 'min:1', 'max:100']]);

        $p = Akreditasi::query()->orderByDesc('id_akreditasi')->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginated($p, $p->getCollection()->map(fn ($a) => self::bentuk($a))->all());
    }

    /** Akreditasi yang sedang berlaku (status aktif). */
    public function aktif(): JsonResponse
    {
        $a = Akreditasi::query()->where('status', true)->orderByDesc('id_akreditasi')->first();

        return $a ? ApiResponse::ok(self::bentuk($a)) : ApiResponse::gagal('Belum ada akreditasi yang aktif');
    }

    public function show(int $id): JsonResponse
    {
        $a = Akreditasi::query()->find($id);

        return $a ? ApiResponse::ok(self::bentuk($a)) : ApiResponse::gagal('Akreditasi tidak ditemukan');
    }

    /** `lembaga` diambil dari kolom keterangan. `nomor_sk` null sampai kolomnya ditambahkan Kelompok 3. */
    public static function bentuk(Akreditasi $a): array
    {
        $berlaku = $a->getAttribute('berlaku_sampai');

        return [
            'id_akreditasi' => $a->id_akreditasi,
            'lembaga' => $a->keterangan,
            'nomor_sk' => $a->getAttribute('nomor_sk'),
            'tahun_terbit' => $a->getAttribute('tahun_terbit'),
            'berlaku_sampai' => $berlaku ? substr((string) $berlaku, 0, 10) : null,
            'status' => (bool) $a->getAttribute('status'),
            'link' => LinkFile::url($a->link),
        ];
    }
}
