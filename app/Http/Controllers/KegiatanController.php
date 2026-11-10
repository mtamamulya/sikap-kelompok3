<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $isDosen = $request->routeIs('dosen.*');
        if (! $isDosen) {
            $submittedActivities = app(PengajuanController::class)->activityOptions()
                ->map(fn (array $activity) => array_merge($activity, ['id' => 'pengajuan-'.$activity['id']]));

            return view('mahasiswa.kegiatan.index', [
                'categoryCounts' => [],
                'categories' => collect([
                    'Lomba', 'Pengabdian', 'Volunteer', 'Kunjungan Industri',
                    'Kuliah Kerja Lapangan', 'Magang', 'Pelatihan', 'Seminar', 'Penelitian',
                ])->merge($submittedActivities->pluck('type'))->unique()->sort()->values(),
                'semesters' => collect(),
                'chartMax' => 20,
                'allActivities' => $submittedActivities,
            ]);
        }

        $kegiatan = Kegiatan::untukUser($request->user())
            ->with(['kategori.jenisKegiatan', 'statusTerkini', 'detail.isianKategori', 'prestasi', 'dokumen', 'anggota.user.mahasiswa', 'anggota.user.dosen'])
            ->withCount('anggota')->orderByDesc('tanggal_mulai')->orderByDesc('id_kegiatan')->get();
        $databaseActivities = $kegiatan->map(fn ($k) => [
            'id' => $isDosen ? $k->getKey() : 'kegiatan-'.$k->getKey(),
            'name' => $k->nama_kegiatan,
            'type' => $k->kategori?->nama_kategori ?? '—',
            'date' => $k->tanggal_mulai?->locale('id')->translatedFormat('d M Y') ?? '—',
            'raw_date' => $k->tanggal_mulai?->toDateString() ?? '',
            'status' => match ($k->statusTerkini?->status) {
                'diajukan' => 'Diajukan',
                'direview' => 'Direview',
                'diterima' => 'Disetujui',
                'direvisi' => 'Perlu revisi',
                'ditolak' => 'Ditolak',
                'selesai' => 'Selesai',
                default => 'Belum diajukan',
            },
            'note' => $k->statusTerkini?->catatan_revisi,
            'capaian' => $k->nilaiIsian('posisi_dalam_kegiatan') ?? '—',
            'tingkat' => ucfirst($k->nilaiIsian('tingkat_kejuaraan') ?? '—'),
            'penyelenggara' => $k->nilaiIsian('penyelenggara') ?? '—',
            'jenis' => $k->prestasi->first()?->jenis_prestasi ?? '—',
            'bentuk' => $k->anggota_count > 1 ? 'Tim' : 'Individu',
            'detail_url' => $isDosen ? route('dosen.kegiatan.show', $k->getKey()) : null,
            'file_name' => $k->dokumen->first()?->nama_file,
            'members' => $k->anggota->map(fn ($member) => [
                'name' => $member->user?->mahasiswa?->nama ?? $member->user?->dosen?->nama ?? $member->user?->email ?? '—',
                'role' => $member->peran,
            ]),
            'documents' => $k->dokumen->map(fn ($document) => [
                'name' => $document->nama_file,
                'type' => $document->tipe_dokumen,
                'url' => $this->documentUrl($document->link_dokumen),
            ]),
        ]);
        $allActivities = $databaseActivities->values();
        $counts = $allActivities->countBy(fn ($activity) => mb_strtolower($activity['type']));
        $categoryCounts = [
            'LOMBA' => $counts->get('lomba', 0),
            'VOLUNTEER' => $counts->get('volunteer', 0),
            'KKL' => $counts->get('kkl', 0) + $counts->get('kuliah kerja lapangan', 0),
            'MAGANG' => $counts->get('magang', 0),
            'PENGABDIAN' => $counts->get('pengabdian', 0),
            'PENELITIAN' => $counts->get('penelitian', 0),
        ];

        // Semester ganjil dimulai Agustus, semester genap Februari.
        $year = $request->user()?->mahasiswa?->tahun_masuk
            ?? (now()->month >= 8 ? now()->year : now()->year - 1);
        $start = Carbon::create($year, 8, 1)->startOfDay();
        $semesterCount = max(6, (int) floor($start->diffInMonths(now(), false) / 6) + 1);
        $semesters = collect(range(1, $semesterCount))->map(function ($number) use ($start, $allActivities) {
            $from = $start->copy()->addMonths(($number - 1) * 6);
            $until = $from->copy()->addMonths(6);

            return ['label' => 'Sem '.$number, 'value' => $allActivities->filter(fn ($activity) => $activity['raw_date'] && Carbon::parse($activity['raw_date'])->gte($from) && Carbon::parse($activity['raw_date'])->lt($until))->count()];
        });

        return view($request->routeIs('dosen.*') ? 'dosen.kegiatan.index' : 'mahasiswa.kegiatan.index', [
            'categoryCounts' => $categoryCounts,
            'categories' => $allActivities->pluck('type')->filter(fn ($category) => $category !== '—')->unique()->sort()->values(),
            'semesters' => $semesters,
            'chartMax' => max(20, (int) (ceil($semesters->max('value') / 4) * 4)),
            'allActivities' => $allActivities,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $kegiatan = Kegiatan::untukUser($request->user())->with([
            'kategori.jenisKegiatan', 'detail.isianKategori', 'statusTerkini', 'dokumen.jenisDokumen',
            'anggota.user.mahasiswa', 'anggota.user.dosen', 'anggota.user.role',
        ])->findOrFail($id);

        return view($request->routeIs('dosen.*') ? 'dosen.kegiatan.show' : 'kegiatan.show', [
            'id' => $kegiatan->getKey(),
            'catatan' => $kegiatan->statusTerkini?->catatan_revisi,
            'k' => [
                'nama' => $kegiatan->nama_kegiatan,
                'kategori' => $kegiatan->kategori?->nama_kategori ?? '—',
                'jenis' => $kegiatan->kategori?->jenisKegiatan?->nama_jenis_kegiatan ?? '—',
                'penyelenggara' => $kegiatan->nilaiIsian('penyelenggara') ?? '—',
                'tahun' => $kegiatan->tanggal_mulai?->year ?? '—',
                'mulai' => $kegiatan->tanggal_mulai?->locale('id')->translatedFormat('j F Y') ?? '—',
                'selesai' => $kegiatan->tanggal_selesai?->locale('id')->translatedFormat('j F Y') ?? '—',
                'status' => ucfirst($kegiatan->statusTerkini?->status ?? 'Belum diajukan'),
            ],
            'anggota' => $kegiatan->anggota->map(fn ($a) => [
                'nama' => $a->user?->mahasiswa?->nama ?? $a->user?->dosen?->nama ?? $a->user?->email ?? '—',
                'status' => $a->user?->role?->nama_role ?? '—',
            ]),
            'dokumen' => $kegiatan->dokumen->map(fn ($d) => [
                'file' => $d->nama_file, 'nama' => $d->nama_file,
                'url' => $this->documentUrl($d->link_dokumen),
                'jenis' => $d->jenisDokumen?->nama_jenis_dokumen ?? $d->tipe_dokumen,
                'keterangan' => null, 'tautan' => $this->documentUrl($d->link_dokumen),
            ]),
        ]);
    }

    private function documentUrl(?string $link): ?string
    {
        if (! $link || $link === '#') {
            return null;
        }
        if (preg_match('~^https?://~i', $link)) {
            return $link;
        }
        if (str_starts_with($link, '/') && ! str_starts_with($link, '//')) {
            return $link;
        }
        if (parse_url($link, PHP_URL_SCHEME) !== null || str_starts_with($link, '//')) {
            return null;
        }

        return Storage::disk('public')->url($link);
    }
}
