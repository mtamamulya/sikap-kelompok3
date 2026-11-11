<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JenisDokumenController extends Controller
{
    public function index()
    {
        $rows = JenisDokumen::all()->map(function ($item) {
            return [
                'id' => $item->id_jenis_dokumen,
                'name' => $item->nama_jenis_dokumen,
                'description' => $item->keterangan, // Sekarang mengambil dari DB
            ];
        })->toArray();

        return view('admin.jenis-dokumen', ['rows' => $rows]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validasi($request);

        if (JenisDokumen::where('nama_jenis_dokumen', $data['name'])->exists()) {
            return $this->duplikat();
        }

        $jenisDokumen = JenisDokumen::create([
            'nama_jenis_dokumen' => $data['name'],
            'keterangan' => $data['description'] ?? null, // Disimpan ke DB
        ]);

        return response()->json([
            'ok' => true,
            'row' => [
                'id' => $jenisDokumen->id_jenis_dokumen,
                'name' => $jenisDokumen->nama_jenis_dokumen,
                'description' => $jenisDokumen->keterangan,
            ],
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $this->validasi($request);
        $jenisDokumen = JenisDokumen::find($id);

        if (! $jenisDokumen) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        if (JenisDokumen::where('nama_jenis_dokumen', $data['name'])->where('id_jenis_dokumen', '!=', $id)->exists()) {
            return $this->duplikat();
        }

        $jenisDokumen->update([
            'nama_jenis_dokumen' => $data['name'],
            'keterangan' => $data['description'] ?? null,
        ]);

        return response()->json([
            'ok' => true,
            'row' => [
                'id' => $jenisDokumen->id_jenis_dokumen,
                'name' => $jenisDokumen->nama_jenis_dokumen,
                'description' => $jenisDokumen->keterangan,
            ],
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $jenisDokumen = JenisDokumen::find($id);

        if (! $jenisDokumen) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $jenisDokumen->delete();

        return response()->json(['ok' => true]);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama jenis dokumen wajib diisi.',
            'name.max' => 'Nama jenis dokumen maksimal 100 karakter.',
        ]);
    }

    private function duplikat(): JsonResponse
    {
        return response()->json(['message' => 'Nama jenis dokumen sudah ada.'], 422);
    }
}
