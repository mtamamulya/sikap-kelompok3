<?php

namespace App\Http\Controllers;

use App\Repositories\DosenStore;
use App\Repositories\MahasiswaStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Halaman "Tambah Kegiatan" (admin): informasi kegiatan, anggota (dosen/mahasiswa), unggah dokumen.
 * TODO-DB: daftar kategori / jenis dokumen di bawah diganti ambil dari KategoriKegiatanStore & JenisDokumen,
 *          dan store() menyimpan kegiatan + anggota + dokumen ke database.
 */
class AdminKegiatanTambahController extends Controller
{
    private const KATEGORI = ['Lomba', 'Seminar', 'Pelatihan', 'Magang', 'Kunjungan Industri'];
    private const JENIS_KEGIATAN = ['Kegiatan Dosen', 'Kegiatan Mahasiswa'];
    private const JENIS_DOKUMEN = ['Surat tugas', 'Sertifikat', 'Proposal', 'Laporan', 'Lainnya'];

    public function __construct(private DosenStore $dosen, private MahasiswaStore $mahasiswa) {}

    /** Form tambah kegiatan. */
    public function create()
    {
        // Kandidat anggota untuk kolom cari: gabungan dosen & mahasiswa (key = "Tipe:id").
        $kandidat = [];
        foreach ($this->dosen->all() as $r) {
            $kandidat[] = ['key' => 'Dosen:'.$r['id'], 'nama' => $r['nama'], 'tipe' => 'Dosen'];
        }
        foreach ($this->mahasiswa->all() as $r) {
            $kandidat[] = ['key' => 'Mahasiswa:'.$r['id'], 'nama' => $r['nama'], 'tipe' => 'Mahasiswa'];
        }

        return view('admin.kegiatan-tambah', [
            'kategori' => self::KATEGORI,
            'jenisKegiatan' => self::JENIS_KEGIATAN,
            'jenisDokumen' => self::JENIS_DOKUMEN,
            'tahun' => range((int) date('Y') + 1, (int) date('Y') - 9),
            'kandidat' => $kandidat,
        ]);
    }

    /** Simpan kegiatan baru (SIMULASI: baru validasi, belum disimpan). */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kategori' => ['required', Rule::in(self::KATEGORI)],
            'jenis_kegiatan' => ['required', Rule::in(self::JENIS_KEGIATAN)],
            'nama_kegiatan' => ['required', 'string', 'max:200'],
            'penyelenggara' => ['required', 'string', 'max:150'],
            'tahun' => ['required', 'integer', 'between:2000,'.((int) date('Y') + 1)],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'anggota' => ['required', 'array', 'min:1'],
            'anggota.*' => ['required', 'regex:/^(Dosen|Mahasiswa):\d+$/'],
            'dokumen' => ['nullable', 'array'],
            'dokumen.*.file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt', 'max:5120'],
            'dokumen.*.nama' => ['nullable', 'required_with:dokumen.*.file', 'string', 'max:150'],
            'dokumen.*.jenis' => ['nullable', 'required_with:dokumen.*.file', Rule::in(self::JENIS_DOKUMEN)],
            'dokumen.*.keterangan' => ['nullable', 'string', 'max:500'],
            'dokumen.*.tautan' => ['nullable', 'url', 'max:255'],
        ], [
            'kategori.required' => 'Kategori wajib dipilih.',
            'jenis_kegiatan.required' => 'Jenis kegiatan wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'penyelenggara.required' => 'Penyelenggara wajib diisi.',
            'tahun.required' => 'Tahun penyelenggaraan wajib dipilih.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'anggota.required' => 'Tambahkan minimal satu anggota kegiatan.',
            'anggota.min' => 'Tambahkan minimal satu anggota kegiatan.',
            'dokumen.*.file.mimes' => 'Format file harus pdf, jpg, jpeg, png, doc, docx, xls, xlsx, atau txt.',
            'dokumen.*.file.max' => 'Ukuran file maksimal 5 MB.',
            'dokumen.*.nama.required_with' => 'Nama dokumen wajib diisi bila ada file.',
            'dokumen.*.jenis.required_with' => 'Jenis dokumen wajib dipilih bila ada file.',
            'dokumen.*.tautan.url' => 'Tautan dokumen harus berupa URL yang valid (https://...).',
        ]);

        // TODO-DB: simpan kegiatan + anggota ($data['anggota'] = ["Dosen:1", "Mahasiswa:5", ...])
        //          + dokumen (file: $request->file('dokumen.N.file')->store('kegiatan', 'public')) dalam satu transaksi.

        $tujuan = $data['jenis_kegiatan'] === 'Kegiatan Dosen' ? 'admin.kegiatan.dosen' : 'admin.kegiatan.mahasiswa';

        return redirect()->route($tujuan)->with('success', 'Kegiatan baru berhasil disimpan.');
    }
}