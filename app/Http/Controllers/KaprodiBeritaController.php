<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Kegiatan;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class KaprodiBeritaController extends Controller
{
    private function opsi(): array
    {
        // PERBAIKAN: Menggunakan relasi 'kategori', bukan 'kegiatan'
        $kegiatanDariDB = Kegiatan::with('kategori')->get();
        
        $kegiatanList = [];
        foreach ($kegiatanDariDB as $k) {
            $kegiatanList[] = [
                'id'       => $k->id_kegiatan,
                'nama'     => $k->nama_kegiatan,
                'kategori' => $k->kategori ? $k->kategori->nama_kategori : 'Lainnya' 
            ];
        }
        
        $kategoriList = Kategori::pluck('nama_kategori')->toArray();
        
        return [
            'kategori'     => !empty($kategoriList) ? $kategoriList : ['Prestasi Mahasiswa', 'Prestasi Dosen', 'Kunjungan Industri', 'Info Akademik', 'Lomba', 'Magang'], 
            'kegiatanList' => $kegiatanList 
        ];
    }

    public function index(Request $request)
    {
        $beritaDb = Berita::with('kegiatan')->orderBy('created_at', 'desc')->get();
        
        $rows = [];
        foreach ($beritaDb as $b) {
            $rows[] = [
                'id'       => $b->id_berita,
                'judul'    => $b->judul_berita,
                'kegiatan' => $b->kegiatan ? $b->kegiatan->nama_kegiatan : 'Mandiri / Tanpa Kegiatan',
                'tanggal'  => $b->tanggal_publish ? Carbon::parse($b->tanggal_publish)->translatedFormat('d F Y') : '-',
                'status'   => ucfirst($b->status), 
            ];
        }

        $kategoriKegiatan = Kategori::pluck('nama_kategori')->toArray();

        return view('kaprodi.berita', [
            'rows'     => $rows, 
            'kegiatan' => !empty($kategoriKegiatan) ? $kategoriKegiatan : ['Prestasi Mahasiswa', 'Prestasi Dosen', 'Kunjungan Industri', 'Info Akademik', 'Lomba', 'Magang']
        ]);
    }

    public function create()
    {
        return view('kaprodi.berita-tambah', $this->opsi());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'isi'         => 'required|string',
            'status'      => 'required|in:publish,Publish,draft,Draft',
            'kegiatan_id' => 'nullable|integer',
            // PERBAIKAN: Ubah 'sampul' jadi 'foto', izinkan pdf, dan naikkan batas ke 5MB (5120 KB)
            'foto'        => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120', 
        ]);

        $status = strtolower($data['status']);
        $sampulPath = null;
        
        // PERBAIKAN: Deteksi file dengan nama 'foto', tapi tetap simpan jalurnya ke variabel sampulPath
        if ($request->hasFile('foto')) {
            $sampulPath = $request->file('foto')->store('berita', 'public');
        }

        Berita::create([
            'judul_berita'    => $data['judul'],
            'isi_berita'      => $data['isi'],
            'status'          => $status,
            'id_kegiatan'     => !empty($data['kegiatan_id']) ? $data['kegiatan_id'] : 1, 
            'tanggal_publish' => $status === 'publish' ? now() : null,
            // Simpan ke kolom 'sampul' di database
            'sampul'          => $sampulPath, 
        ]);

        return redirect('/kaprodi/berita')->with('status', 'Berita berhasil disimpan.');
    }

    public function show(string $id)
    {
        $beritaDb = Berita::with('kegiatan')->findOrFail($id);

        // Siapkan variabel fotoUrl agar bisa dimasukkan ke dalam array $berita dan dikirim secara mandiri
        $fotoUrl = $beritaDb->sampul ? asset('storage/' . $beritaDb->sampul) : null;

        $berita = [
            'id'       => $beritaDb->id_berita,
            'judul'    => $beritaDb->judul_berita,
            'isi'      => $beritaDb->isi_berita,
            'status'   => ucfirst($beritaDb->status),
            'kegiatan' => $beritaDb->kegiatan ? $beritaDb->kegiatan->nama_kegiatan : 'Mandiri / Tanpa Kegiatan',
            'tanggal'  => $beritaDb->tanggal_publish,
            'foto'     => $fotoUrl, // <-- PERBAIKAN: Kunci ini ditambahkan agar Blade tidak error
        ];

        $tanggal = $berita['tanggal'] ? Carbon::parse($berita['tanggal'])->locale('id') : null;
        $kata = str_word_count(strip_tags($berita['isi']));

        // PENTING: Untuk file KaprodiBeritaController, ganti 'admin.berita-detail' menjadi 'kaprodi.berita-detail'
        return view('kaprodi.berita-detail', [
            'berita'        => $berita,
            'paragraf'      => array_values(array_filter(preg_split('/\R{1,}/', trim($berita['isi'])))),
            'foto'          => $fotoUrl, 
            'hariTanggal'   => $tanggal ? $tanggal->translatedFormat('l, j F Y') : '-',
            'tanggalPendek' => $tanggal ? $tanggal->format('d/m/Y') : '-',
            'waktuBaca'     => max(1, (int) ceil($kata / 200)).' menit',
            'terkait'       => [],
        ]);
    }

    public function edit(string $id)
    {
        $beritaDb = Berita::findOrFail($id);

        $berita = [
            'id'          => $beritaDb->id_berita,
            'judul'       => $beritaDb->judul_berita,
            'isi'         => $beritaDb->isi_berita,
            'status'      => strtolower($beritaDb->status), 
            'kegiatan_id' => $beritaDb->id_kegiatan,
            'sumber'      => $beritaDb->id_kegiatan ? 'prev' : 'none',
        ];

        return view('kaprodi.berita-edit', $this->opsi() + ['berita' => $berita]);
    }

    public function update(Request $request, string $id)
    {
        $beritaDb = Berita::findOrFail($id);

        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'isi'         => 'required|string',
            'status'      => 'required|in:publish,Publish,draft,Draft',
            'kegiatan_id' => 'nullable|integer',
            // PERBAIKAN: Sesuaikan juga di sini
            'foto'        => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120', 
        ]);

        $status = strtolower($data['status']);
        
        // PERBAIKAN: Deteksi input 'foto'
        if ($request->hasFile('foto')) {
            if ($beritaDb->sampul) {
                Storage::disk('public')->delete($beritaDb->sampul);
            }
            $beritaDb->sampul = $request->file('foto')->store('berita', 'public');
        }

        $beritaDb->judul_berita = $data['judul'];
        $beritaDb->isi_berita   = $data['isi'];
        $beritaDb->status       = $status;
        $beritaDb->id_kegiatan  = !empty($data['kegiatan_id']) ? $data['kegiatan_id'] : 1;
        
        if ($status === 'publish' && !$beritaDb->tanggal_publish) {
            $beritaDb->tanggal_publish = now();
        } elseif ($status === 'draft') {
            $beritaDb->tanggal_publish = null;
        }

        $beritaDb->save();

        return redirect('/kaprodi/berita')->with('status', 'Berita berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $beritaDb = Berita::findOrFail($id);
        if ($beritaDb->sampul) {
            Storage::disk('public')->delete($beritaDb->sampul);
        }
        $beritaDb->delete();

        return redirect('/kaprodi/berita')->with('status', 'Berita berhasil dihapus.');
    }
}