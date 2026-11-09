<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\EnrollmentKelasMataKuliah;
use App\Models\JenisDokumen;
use App\Models\Pengajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenPengajaranController extends Controller
{
    /**
     * Tampilkan halaman daftar pengajaran dosen.
     */
    public function index()
    {
        $pengajaran = Pengajaran::with(['enrollmentKelasMataKuliah', 'dokumen'])
            ->latest()
            ->get();

        return view('dosen.pengajaran.index', compact('pengajaran'));
    }

    /**
     * Tampilkan form tambah pengajaran.
     */
    public function create()
    {
        $daftarTahunAkademik = ['2025/2026 Ganjil', '2025/2026 Genap', '2024/2025 Ganjil', '2024/2025 Genap'];
        $daftarProdi = ['D3 Teknik Informatika', 'D4 Teknik Informatika', 'D3 Teknik Elektronika', 'D4 Teknik Telekomunikasi'];
        $daftarKelas = ['1A', '1B', '1C', '2A', '2B', '2C', '3A', '3B', '3C'];
        $daftarMatkul = ['Kecerdasan Buatan', 'Jaringan Komputer', 'Sistem Basis Data', 'Internet of Things', 'Computer Vision', 'Sistem Operasi'];

        $dosenLogin = Auth::user()->name ?? Auth::user()->nama ?? 'Kuwat Santoso';

        return view('dosen.pengajaran.create', compact(
            'daftarTahunAkademik',
            'daftarProdi',
            'daftarKelas',
            'daftarMatkul',
            'dosenLogin'
        ));
    }

    /**
     * Simpan data pengajaran dan dokumen pendukung ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data Form
        $request->validate([
            'tahun_akademik'  => 'required|string',
            'program_studi'   => 'required|string',
            'kelas'           => 'required|string',
            'mata_kuliah'     => 'required|string',
            'dokumen'         => 'required|array|min:1',
            'dokumen.*.nama'   => 'required|string|max:255',
            'dokumen.*.jenis'  => 'required|string',
            'dokumen.*.tautan' => 'required|url',
        ], [
            'program_studi.required'  => 'Program studi wajib dipilih!',
            'kelas.required'          => 'Kelas wajib dipilih!',
            'mata_kuliah.required'    => 'Mata kuliah wajib dipilih!',
            'dokumen.*.nama.required' => 'Nama dokumen wajib diisi!',
            'dokumen.*.tautan.url'    => 'Tautan dokumen harus berupa URL valid!',
        ]);

        DB::beginTransaction();

        try {
            // 2. Ambil atau buat data Enrollment Kelas Mata Kuliah
            $enrollment = EnrollmentKelasMataKuliah::firstOrCreate([
                'mata_kuliah' => $request->mata_kuliah,
                'kelas'       => $request->kelas,
                'prodi'       => $request->program_studi,
            ]);

            // 3. Loop untuk simpan tiap dokumen pendukung
            foreach ($request->dokumen as $dok) {
                // Cari ID jenis dokumen berdasarkan pilihan di dropdown form
                $jenisDok = JenisDokumen::where('nama_jenis_dokumen', $dok['jenis'])
                    ->orWhere('id_jenis_dokumen', $dok['jenis'])
                    ->value('id_jenis_dokumen') ?? 1;

                // Simpan ke tabel `dokumen` (sesuai kolom $fillable model Dokumen timmu)
                $dokumenBaru = Dokumen::create([
                    'id_jenis_dokumen' => $jenisDok,
                    'nama_file'        => $dok['nama'],
                    'link_dokumen'      => $dok['tautan'],
                    'tipe_dokumen'      => 'url',
                    'is_lampiran'      => false,
                ]);

                // 4. Hubungkan ke junction table `pengajaran`
                Pengajaran::create([
                    'id_enrollment_kelas_mk' => $enrollment->id_enrollment_kelas_mk,
                    'id_dokumen'             => $dokumenBaru->id_dokumen,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pengajaran.index')
                ->with('success', 'Data pengajaran berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data pengajaran: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail data pengajaran.
     */
    public function show($id)
    {
        $pengajaran = Pengajaran::with(['enrollmentKelasMataKuliah', 'dokumen'])->findOrFail($id);

        return view('dosen.pengajaran.show', compact('pengajaran'));
    }

    /**
     * Tampilkan form edit pengajaran.
     */
    public function edit($id)
    {
        $pengajaran = Pengajaran::with(['enrollmentKelasMataKuliah', 'dokumen'])->findOrFail($id);

        return view('dosen.pengajaran.edit', compact('pengajaran'));
    }

    /**
     * Update data pengajaran.
     */
    public function update(Request $request, $id)
    {
        // Fitur update bisa disesuaikan nanti saat tes edit
        return redirect()->route('pengajaran.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Hapus data pengajaran.
     */
    public function destroy($id)
    {
        $pengajaran = Pengajaran::findOrFail($id);
        $pengajaran->delete();

        return redirect()->route('pengajaran.index')->with('success', 'Data pengajaran berhasil dihapus!');
    }
}