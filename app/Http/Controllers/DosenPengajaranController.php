<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentKelasMataKuliah;
use App\Models\EnrollmentMataKuliah;
use App\Models\EnrollmentTingkatKelas;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Pengajaran dosen = kelas + mata kuliah yang diampu dosen pada satu tahun akademik
 * (tabel enrollment_kelas_mata_kuliah). Dokumen pendukung (tabel pengajaran -> dokumen)
 * belum dikerjakan: menunggu keputusan tim database soal dokumen.id_kegiatan.
 */
class DosenPengajaranController extends Controller
{
    /** Relasi yang dibutuhkan untuk menampilkan satu baris pengajaran. */
    private const RELASI = [
        'dosen',
        'enrollmentMataKuliah.mataKuliah',
        'enrollmentMataKuliah.tahunAkademik',
        'enrollmentTingkatKelas.prodi',
        'enrollmentTingkatKelas.tingkat',
        'enrollmentTingkatKelas.kelas',
        'pengajaran.dokumen.jenisDokumen',
    ];

    public function index(Request $request)
    {
        $dosen = $request->user()?->dosen;

        $pengajaran = $dosen
            ? EnrollmentKelasMataKuliah::with(self::RELASI)
                ->where('id_dosen', $dosen->id_dosen)
                ->get()
                ->map(fn (EnrollmentKelasMataKuliah $item) => $this->baris($item))
                ->sortByDesc('tahun')
                ->values()
                ->all()
            : [];

        $kolom = collect($pengajaran);

        return view('dosen.pengajaran.index', [
            'pengajaran' => $pengajaran,
            'daftarKelas' => $kolom->pluck('kelas')->unique()->sort()->values()->all(),
            'daftarTahun' => $kolom->pluck('tahun')->unique()->sortDesc()->values()->all(),
            'daftarSemester' => ['Ganjil', 'Genap'],
        ]);
    }

    public function create(Request $request)
    {
        return view('dosen.pengajaran.create', [
            'opsi' => $this->opsiForm(),
            'dosenLogin' => $request->user()?->dosen?->nama ?? '-',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dosen = $this->dosenLogin($request);
        $data = $this->validasi($request, (int) $dosen->id_dosen);

        $item = EnrollmentKelasMataKuliah::create($data + ['id_dosen' => $dosen->id_dosen]);

        return redirect()->route('pengajaran.show', $item->getKey())
            ->with('sukses', 'Pengajaran berhasil disimpan.');
    }

    public function show(Request $request, int $id)
    {
        $item = $this->milikDosen($request, $id);

        return view('dosen.pengajaran.show', [
            'p' => $this->detail($item),
            'dokumen' => $this->dokumen($item),
        ]);
    }

    public function edit(Request $request, int $id)
    {
        $item = $this->milikDosen($request, $id);

        return view('dosen.pengajaran.edit', [
            'item' => $item,
            'opsi' => $this->opsiForm(),
            'dosenLogin' => $item->dosen->nama,
            'dokumen' => $this->dokumen($item),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = $this->milikDosen($request, $id);
        $data = $this->validasi($request, (int) $item->id_dosen, $item);

        $item->update($data);

        return redirect()->route('pengajaran.show', $item->getKey())
            ->with('sukses', 'Perubahan pengajaran berhasil disimpan.');
    }

    // ---------------------------------------------------------------- helper

    /** Dosen dari akun yang login. 403 kalau akun bukan dosen (atau belum login). */
    private function dosenLogin(Request $request)
    {
        return $request->user()?->dosen ?? abort(403, 'Akun ini bukan akun dosen.');
    }

    /** Pengajaran milik dosen yang login. Milik dosen lain atau tidak ada = 404. */
    private function milikDosen(Request $request, int $id): EnrollmentKelasMataKuliah
    {
        $dosen = $request->user()?->dosen;
        abort_if($dosen === null, 404);

        return EnrollmentKelasMataKuliah::with(self::RELASI)
            ->where('id_dosen', $dosen->id_dosen)
            ->findOrFail($id);
    }

    /**
     * Validasi form tambah / edit.
     * Aturan: rombel dan mata kuliah harus dari tahun akademik yang sama, dan satu mata kuliah
     * pada satu rombel hanya boleh punya satu dosen pengampu (unique di tabel).
     */
    private function validasi(Request $request, int $idDosen, ?EnrollmentKelasMataKuliah $ubah = null): array
    {
        $data = $request->validate([
            'id_enrollment_tingkat_kelas' => ['required', 'integer', 'exists:enrollment_tingkat_kelas,id_enrollment_tingkat_kelas'],
            'id_enrollment_mk' => ['required', 'integer', 'exists:enrollment_mata_kuliah,id_enrollment_mk'],
        ], [
            'id_enrollment_tingkat_kelas.required' => 'Kelas wajib dipilih.',
            'id_enrollment_tingkat_kelas.exists' => 'Kelas yang dipilih tidak ditemukan.',
            'id_enrollment_mk.required' => 'Mata kuliah wajib dipilih.',
            'id_enrollment_mk.exists' => 'Mata kuliah yang dipilih tidak ditemukan.',
        ]);

        $rombel = EnrollmentTingkatKelas::findOrFail($data['id_enrollment_tingkat_kelas']);
        $mataKuliah = EnrollmentMataKuliah::findOrFail($data['id_enrollment_mk']);

        if ((int) $rombel->id_tahun_akademik !== (int) $mataKuliah->id_tahun_akademik) {
            throw ValidationException::withMessages([
                'id_enrollment_mk' => 'Mata kuliah ini tidak ditawarkan pada tahun akademik kelas yang dipilih.',
            ]);
        }

        $sudahAda = EnrollmentKelasMataKuliah::query()
            ->where('id_enrollment_tingkat_kelas', $rombel->getKey())
            ->where('id_enrollment_mk', $mataKuliah->getKey())
            ->when($ubah, fn ($q) => $q->whereKeyNot($ubah->getKey()))
            ->first();

        if ($sudahAda) {
            throw ValidationException::withMessages([
                'id_enrollment_mk' => (int) $sudahAda->id_dosen === $idDosen
                    ? 'Anda sudah mengampu mata kuliah ini di kelas tersebut.'
                    : 'Mata kuliah ini di kelas tersebut sudah diampu dosen lain.',
            ]);
        }

        return $data;
    }

    /** Pilihan untuk form: tahun akademik, kelas (rombel), dan mata kuliah yang ditawarkan. */
    private function opsiForm(): array
    {
        return [
            'tahun' => TahunAkademik::orderByDesc('status')->orderByDesc('id_tahun_akademik')->get()
                ->map(fn (TahunAkademik $t) => [
                    'id' => $t->id_tahun_akademik,
                    'label' => $t->tahun_akademik,
                    'semester' => $this->pecahTahun($t->tahun_akademik)['semester'],
                ])->all(),
            'rombel' => EnrollmentTingkatKelas::with(['prodi', 'tingkat', 'kelas'])->get()
                ->map(fn (EnrollmentTingkatKelas $r) => [
                    'id' => $r->id_enrollment_tingkat_kelas,
                    'ta' => $r->id_tahun_akademik,
                    'prodi' => $r->prodi->jenjang.' '.$r->prodi->nama_prodi,
                    'kelas' => $r->tingkat->tingkat.$r->kelas->jenis_kelas,
                ])->sortBy('kelas')->values()->all(),
            'matkul' => EnrollmentMataKuliah::with('mataKuliah')->get()
                ->map(fn (EnrollmentMataKuliah $m) => [
                    'id' => $m->id_enrollment_mk,
                    'ta' => $m->id_tahun_akademik,
                    'label' => $m->mataKuliah->nama_mk.' ('.$m->mataKuliah->kode_mk.')',
                ])->sortBy('label')->values()->all(),
        ];
    }

    /** Satu baris untuk tabel daftar pengajaran. */
    private function baris(EnrollmentKelasMataKuliah $item): array
    {
        $ta = $this->pecahTahun($item->enrollmentMataKuliah->tahunAkademik->tahun_akademik);

        return [
            'id' => $item->getKey(),
            'matkul' => $item->enrollmentMataKuliah->mataKuliah->nama_mk,
            'kelas' => $this->namaKelas($item),
            'tahun' => $ta['tahun'],
            'semester' => $ta['semester'],
        ];
    }

    /** Data halaman detail. */
    private function detail(EnrollmentKelasMataKuliah $item): array
    {
        $mk = $item->enrollmentMataKuliah->mataKuliah;
        $ta = $item->enrollmentMataKuliah->tahunAkademik->tahun_akademik;
        $pecah = $this->pecahTahun($ta);

        return [
            'tahun_akademik' => $pecah['tahun'].' '.$pecah['semester'],
            'tahun' => $pecah['tahun'],
            'semester' => $pecah['semester'],
            'prodi' => $item->enrollmentTingkatKelas->prodi->nama_prodi,
            'kelas' => 'Kelas '.$this->namaKelas($item),
            'matkul' => $mk->nama_mk,
            'dosen' => $item->dosen->nama.' (akun Anda)',
            'kode' => $mk->kode_mk,
            'sks' => $mk->sks_teori + $mk->sks_praktik,
            'dosen_lengkap' => $item->dosen->nama,
        ];
    }

    /** Dokumen pendukung (kosong sampai fitur dokumen dikerjakan). */
    private function dokumen(EnrollmentKelasMataKuliah $item): array
    {
        return $item->pengajaran
            ->map(fn ($p) => [
                'nama' => $p->dokumen->nama_file,
                'jenis' => $p->dokumen->jenisDokumen?->nama_jenis_dokumen ?? '-',
                'keterangan' => '',
                'tautan' => $p->dokumen->link_dokumen,
            ])->all();
    }

    /** "2A" = tingkat 2 kelas A. */
    private function namaKelas(EnrollmentKelasMataKuliah $item): string
    {
        return $item->enrollmentTingkatKelas->tingkat->tingkat.$item->enrollmentTingkatKelas->kelas->jenis_kelas;
    }

    /** "Ganjil 2025/2026" -> ['tahun' => '2025/2026', 'semester' => 'Ganjil']. */
    private function pecahTahun(string $tahunAkademik): array
    {
        preg_match('~\d{4}\s*/\s*\d{4}~', $tahunAkademik, $tahun);
        preg_match('~ganjil|genap~i', $tahunAkademik, $semester);

        return [
            'tahun' => isset($tahun[0]) ? preg_replace('~\s+~', '', $tahun[0]) : $tahunAkademik,
            'semester' => isset($semester[0]) ? ucfirst(strtolower($semester[0])) : '-',
        ];
    }
}
