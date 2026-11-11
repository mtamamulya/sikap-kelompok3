<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Support\ApiResponse;
use App\Support\LinkFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** GET /api/v1/kurikulum, /kurikulum/{id}, /kurikulum/{id}/mata-kuliah (baca saja, tanpa token). */
class KurikulumController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', 'in:aktif,nonaktif'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $p = Kurikulum::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status') === 'aktif'))
            ->when($request->filled('q'), fn ($q) => $q->where('nama_kurikulum', 'like', '%'.$request->query('q').'%'))
            ->orderByDesc('id_kurikulum')
            ->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginated($p, $p->getCollection()->map(fn ($k) => self::bentuk($k))->all());
    }

    public function show(int $id): JsonResponse
    {
        $k = Kurikulum::query()->find($id);

        return $k ? ApiResponse::ok(self::bentuk($k)) : ApiResponse::gagal('Kurikulum tidak ditemukan');
    }

    /** Mata kuliah satu kurikulum, urut semester lalu kode. Filter opsional: ?semester=1..8 */
    public function mataKuliah(Request $request, int $id): JsonResponse
    {
        $request->validate(['semester' => ['nullable', 'integer', 'between:1,8']]);

        if (! Kurikulum::query()->whereKey($id)->exists()) {
            return ApiResponse::gagal('Kurikulum tidak ditemukan');
        }

        $rows = MataKuliah::query()
            ->where('id_kurikulum', $id)
            ->when($request->filled('semester'), fn ($q) => $q->where('semester', (int) $request->query('semester')))
            ->orderBy('semester')->orderBy('kode_mk')
            ->get()
            ->map(fn ($m) => MataKuliahController::bentuk($m))
            ->all();

        return ApiResponse::ok($rows);
    }

    /** Nama kunci mengikuti kolom database. Kolom tahun_terbit dan deskripsi bernilai null sampai migration usulan dipasang. */
    public static function bentuk(Kurikulum $k): array
    {
        return [
            'id_kurikulum' => $k->id_kurikulum,
            'id_prodi' => $k->id_prodi,
            'nama_kurikulum' => $k->nama_kurikulum,
            'tahun_terbit' => $k->getAttribute('tahun_terbit'),
            'deskripsi' => $k->getAttribute('deskripsi'),
            'status' => (bool) $k->status,
            'link' => LinkFile::url($k->link),
        ];
    }
}
