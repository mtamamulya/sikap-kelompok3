<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user() ?? $request->user();

        $kegiatan = Kegiatan::with(['dokumen.jenisDokumen', 'kategori'])
            ->when($user, function ($query) use ($user) {
                return $query->untukUser($user);
            })
            ->latest('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kegiatan berhasil diambil.',
            'data'    => $kegiatan
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_user'                    => 'nullable|exists:users,id_user',
            'id_kategori'                => 'required|exists:kategori,id_kategori',
            'id_kegiatan_sebelumnya'     => 'nullable|exists:kegiatan,id_kegiatan',
            'nama_kegiatan'              => 'required|string|max:255',
            'lokasi'                     => 'nullable|string|max:255',
            'deskripsi'                  => 'nullable|string',
            'nomor_sk'                   => 'nullable|string|max:100',
            'tanggal_sk'                 => 'nullable|date',
            'tanggal_mulai'              => 'nullable|date',
            'tanggal_selesai'            => 'nullable|date|after_or_equal:tanggal_mulai',
            'dokumen'                    => 'required|array|min:1',
            'dokumen.*.id_jenis_dokumen' => 'required|exists:jenis_dokumen,id_jenis_dokumen',
            'dokumen.*.link_dokumen'     => 'required|url',
            'dokumen.*.id_detail_kegiatan' => 'nullable|exists:detail_kegiatan,id_detail_kegiatan',
            'dokumen.*.is_lampiran'        => 'nullable|boolean',
        ], [
            'dokumen.required'           => 'Dokumen pendukung wajib diisi minimal 1 data.',
            'dokumen.*.link_dokumen.url' => 'Format tautan dokumen harus berupa URL valid (http/https).',
        ]);

        $userId = Auth::id() ?? $request->id_user;

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi dan id_user tidak diberikan.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $kegiatan = Kegiatan::create([
                'id_user'                => $userId,
                'id_kategori'            => $validatedData['id_kategori'],
                'id_kegiatan_sebelumnya' => $validatedData['id_kegiatan_sebelumnya'] ?? null,
                'nama_kegiatan'          => $validatedData['nama_kegiatan'],
                'lokasi'                 => $validatedData['lokasi'] ?? null,
                'deskripsi'              => $validatedData['deskripsi'] ?? null,
                'nomor_sk'               => $validatedData['nomor_sk'] ?? null,
                'tanggal_sk'             => $validatedData['tanggal_sk'] ?? null,
                'tanggal_mulai'          => $validatedData['tanggal_mulai'] ?? null,
                'tanggal_selesai'        => $validatedData['tanggal_selesai'] ?? null,
            ]);

            foreach ($validatedData['dokumen'] as $doc) {
                Dokumen::create([
                    'id_kegiatan'        => $kegiatan->id_kegiatan,
                    'id_jenis_dokumen'   => $doc['id_jenis_dokumen'],
                    'link_dokumen'       => $doc['link_dokumen'],
                    'id_detail_kegiatan' => $doc['id_detail_kegiatan'] ?? null,
                    'is_lampiran'        => $doc['is_lampiran'] ?? false,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan dan dokumen pendukung berhasil disimpan!',
                'data'    => $kegiatan->load(['dokumen.jenisDokumen', 'kategori'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $kegiatan = Kegiatan::with(['dokumen.jenisDokumen', 'kategori', 'statusTerkini'])->find($id);

        if (!$kegiatan) {
            return response()->json([
                'success' => false,
                'message' => 'Data kegiatan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kegiatan berhasil diambil.',
            'data'    => $kegiatan
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::find($id);

        if (!$kegiatan) {
            return response()->json([
                'success' => false,
                'message' => 'Data kegiatan tidak ditemukan.'
            ], 404);
        }

        $validatedData = $request->validate([
            'id_kategori'                => 'sometimes|required|exists:kategori,id_kategori',
            'nama_kegiatan'              => 'sometimes|required|string|max:255',
            'lokasi'                     => 'nullable|string|max:255',
            'deskripsi'                  => 'nullable|string',
            'nomor_sk'                   => 'nullable|string|max:100',
            'tanggal_sk'                 => 'nullable|date',
            'tanggal_mulai'              => 'nullable|date',
            'tanggal_selesai'            => 'nullable|date|after_or_equal:tanggal_mulai',
            'dokumen'                    => 'nullable|array',
            'dokumen.*.id_jenis_dokumen' => 'required_with:dokumen|exists:jenis_dokumen,id_jenis_dokumen',
            'dokumen.*.link_dokumen'     => 'required_with:dokumen|url',
        ]);

        DB::beginTransaction();

        try {
            $kegiatan->update($validatedData);

            if (isset($validatedData['dokumen'])) {
                Dokumen::where('id_kegiatan', $kegiatan->id_kegiatan)->delete();
                foreach ($validatedData['dokumen'] as $doc) {
                    Dokumen::create([
                        'id_kegiatan'      => $kegiatan->id_kegiatan,
                        'id_jenis_dokumen' => $doc['id_jenis_dokumen'],
                        'link_dokumen'     => $doc['link_dokumen'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil diperbarui!',
                'data'    => $kegiatan->load(['dokumen.jenisDokumen', 'kategori'])
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::find($id);

        if (!$kegiatan) {
            return response()->json([
                'success' => false,
                'message' => 'Data kegiatan tidak ditemukan.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            Dokumen::where('id_kegiatan', $kegiatan->id_kegiatan)->delete();
            $kegiatan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan dan dokumen pendukung berhasil dihapus.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}