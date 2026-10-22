<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    private function rows(): Collection
    {
        $status = ['Disetujui', 'Direview', 'Disetujui', 'Selesai', 'Direview', 'Ditolak', 'Direview', 'Selesai', 'Disetujui', 'Perlu revisi', 'Direview', 'Selesai'];

        return collect($status)->map(fn ($s, $i) => [
            'id' => $i + 1,
            'title' => 'Pengajuan Lomba KMPIN VII 2026',
            'category' => 'Lomba',
            'type' => 'Dukungan lomba mahasiswa',
            'status' => $s,
            'submitted_at' => Carbon::create(2024, 3, 12 + $i),
        ])->sortByDesc('submitted_at')->values();
    }

    public function index(Request $request)
    {
        $all = $this->rows();
        $q = trim($request->string('q')->toString());
        $status = $request->string('status')->toString();
        $isDate = fn ($v) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
        $from = $isDate($request->string('from')->toString());
        $to = $isDate($request->string('to')->toString());

        $filtered = $all->filter(function ($r) use ($q, $status, $from, $to) {
            $date = $r['submitted_at']->toDateString();

            return (!$status || $r['status'] === $status)
                && (!$q || str_contains(mb_strtolower($r['title'].' '.$r['category']), mb_strtolower($q)))
                && (!$from || $date >= $from)
                && (!$to || $date <= $to);
        })->values();

        $perPage = 10;
        $page = max(1, (int) $request->input('page', 1));
        $rows = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $counts = $all->countBy('status');

        return view('pengajuan.index', compact('rows', 'all', 'counts', 'q', 'status', 'from', 'to'));
    }

    public function create()
    {
        return view('pengajuan.create');
    }

    public function show(int $id)
    {
        $row = $this->rows()->firstWhere('id', $id);
        abort_if(!$row, 404);

        $flows = [
            'Direview' => ['Diajukan', 'Direview'],
            'Disetujui' => ['Diajukan', 'Direview', 'Diterima'],
            'Selesai' => ['Diajukan', 'Direview', 'Diterima', 'Selesai'],
            'Ditolak' => ['Diajukan', 'Direview', 'Ditolak'],
            'Perlu revisi' => ['Diajukan', 'Direview', 'Direvisi'],
        ];

        $d = $row['submitted_at'];
        $meta = [
            'Diajukan' => [$d->copy()->setTime(9, 0), 'Arya Bima', 'Pengajuan dukungan lomba dikirim untuk ditinjau.'],
            'Direview' => [$d->copy()->setTime(13, 0), 'Tim administrasi', 'Verifikasi persyaratan dan periode pengajuan.'],
            'Diterima' => [$d->copy()->addDay()->setTime(10, 15), 'Reviewer akademik', 'Pengajuan disetujui. Menunggu penerbitan dokumen hasil.'],
            'Ditolak' => [$d->copy()->addDay()->setTime(10, 30), 'Reviewer akademik', 'Pengajuan di luar periode penerimaan. Proses dihentikan.'],
            'Direvisi' => [$d->copy()->addDay()->setTime(10, 0), 'Reviewer akademik', 'Rincian anggaran dan lembar pengesahan perlu diperbaiki.'],
            'Selesai' => [$d->copy()->addDays(2)->setTime(14, 0), 'Tim administrasi', 'Dokumen diterbitkan dan proses pengajuan ditutup.'],
        ];

        $steps = collect($flows[$row['status']])->map(fn ($s) => [
            'title' => $s, 'at' => $meta[$s][0], 'actor' => $meta[$s][1], 'note' => $meta[$s][2],
        ]);

        $student = ['name' => 'Arya Bima', 'nim' => '43325115', 'prodi' => 'Teknologi Rekayasa Komputer'];

        $files = [
            ['name' => 'Dokumen_pengajuan_Arya.pdf', 'meta' => 'PDF • 1,2 MB'],
            ['name' => 'Lampiran_pendukung.pdf', 'meta' => 'PDF • 840 KB'],
        ];

        return view('pengajuan.show', compact('row', 'steps', 'student', 'files'));
    }

    public function store(Request $request)
    {
        $request->merge(['category' => $request->input('category') ?: 'Lainnya']);

        $members = ['exclude_unless:participation,Tim', 'required', 'json', function ($attr, $value, $fail) {
            if (count(json_decode($value, true) ?? []) < 1) {
                $fail('Tambahkan minimal satu anggota tim.');
            }
        }];

        $common = [
            'category' => ['required', Rule::in(['Lomba', 'Volunteer', 'Magang', 'Lainnya'])],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['required', 'string', 'max:1000'],
            'document' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ];

        $specific = match ($request->input('category')) {
            'Lomba' => [
                'title' => ['required', 'string', 'max:255'],
                'participation' => ['required', Rule::in(['Individu', 'Tim'])],
                'members' => $members,
                'level' => ['required', Rule::in(['Kabupaten', 'Provinsi', 'Nasional', 'Internasional'])],
                'organizer' => ['required', 'string', 'max:255'],
                'info_url' => ['nullable', 'url'],
                'advisor' => ['nullable', 'string', 'max:255'],
            ],
            'Volunteer' => [
                'title' => ['required', 'string', 'max:255'],
                'organizer' => ['required', 'string', 'max:255'],
                'role' => ['required', 'string', 'max:255'],
            ],
            'Magang' => [
                'role' => ['required', 'string', 'max:255'],
                'organizer' => ['required', 'string', 'max:255'],
                'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
            ],
            default => [
                'title' => ['required', 'string', 'max:255'],
                'organizer' => ['required', 'string', 'max:255'],
                'scope' => ['required', Rule::in(['Kampus', 'Regional', 'Nasional', 'Internasional'])],
                'role' => ['required', Rule::in(['Peserta', 'Panitia', 'Narasumber', 'Lainnya'])],
                'participation' => ['required', Rule::in(['Individu', 'Tim'])],
                'members' => $members,
                'info_url' => ['nullable', 'url'],
                'advisor' => ['nullable', 'string', 'max:255'],
            ],
        };

        $data = $request->validate($common + $specific);

        if ($data['category'] === 'Magang') {
            $data['title'] = 'Magang '.$data['role'].' di '.$data['organizer'];
        }

        if (isset($data['members'])) {
            $data['members'] = json_decode($data['members'], true);
        }

        $data['document_path'] = $request->file('document')->store('pengajuan', 'public');
        unset($data['document']);

        // TODO: Submission::create($data + ['user_id' => auth()->id(), 'status' => 'Direview']);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim.');
    }
}