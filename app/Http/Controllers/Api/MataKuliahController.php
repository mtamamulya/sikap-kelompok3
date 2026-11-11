<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** GET /api/v1/mata-kuliah (baca saja, tanpa token). Filter: id_kurikulum, semester, q (kode atau nama). */
class MataKuliahController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'id_kurikulum' => ['nullable', 'integer'],
            'semester' => ['nullable', 'integer', 'between:1,8'],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $p = MataKuliah::query()
            ->when($request->filled('id_kurikulum'), fn ($q) => $q->where('id_kurikulum', (int) $request->query('id_kurikulum')))
            ->when($request->filled('semester'), fn ($q) => $q->where('semester', (int) $request->query('semester')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $kata = '%'.$request->query('q').'%';
                $q->where(fn ($w) => $w->where('kode_mk', 'like', $kata)->orWhere('nama_mk', 'like', $kata));
            })
            ->orderBy('id_kurikulum')->orderBy('semester')->orderBy('kode_mk')
            ->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginated($p, $p->getCollection()->map(fn ($m) => self::bentuk($m))->all());
    }

    public function show(int $id): JsonResponse
    {
        $m = MataKuliah::query()->find($id);

        return $m ? ApiResponse::ok(self::bentuk($m)) : ApiResponse::gagal('Mata kuliah tidak ditemukan');
    }

    /** Nama kunci mengikuti kolom database (perhatikan: sks_praktik tetapi jam_praktek). */
    public static function bentuk(MataKuliah $m): array
    {
        return [
            'id_mk' => $m->id_mk,
            'id_kurikulum' => $m->id_kurikulum,
            'semester' => (int) $m->semester,
            'kode_mk' => $m->kode_mk,
            'nama_mk' => $m->nama_mk,
            'sks_teori' => (int) $m->sks_teori,
            'sks_praktik' => (int) $m->sks_praktik,
            'jam_teori' => (int) $m->jam_teori,
            'jam_praktek' => (int) $m->jam_praktek,
        ];
    }
}
