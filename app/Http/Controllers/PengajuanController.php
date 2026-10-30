<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    private const KEY = 'pengajuan.items';

    private const EDITABLE = ['Diajukan', 'Direview', 'Perlu revisi'];

    private function items(): array
    {
        return session(self::KEY, []);
    }

    private function dummyItems(): Collection
    {
        $entries = [
            [
                'status' => 'Selesai',
                'submitted_at' => '2026-02-12T09:00:00+07:00',
                'category' => 'Lomba',
                'title' => 'Pengajuan Lomba KMPIN VII 2026',
                'organizer' => 'Kemendiktisaintek',
                'location' => 'Semarang',
                'start_date' => '2026-03-10',
                'end_date' => '2026-03-12',
                'description' => 'Partisipasi dalam Kompetisi Mahasiswa Informatika Politeknik Nasional 2026.',
                'participation' => 'Tim',
                'members' => [['name' => 'Arya Bima', 'role' => 'Ketua'], ['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota']],
                'level' => 'Nasional',
            ],
            [
                'status' => 'Disetujui',
                'submitted_at' => '2026-03-03T10:30:00+07:00',
                'category' => 'Lomba',
                'title' => 'Gemastik XIX - Divisi Pengembangan Aplikasi',
                'organizer' => 'Pusat Prestasi Nasional',
                'location' => 'Yogyakarta',
                'start_date' => '2026-04-14',
                'end_date' => '2026-04-16',
                'description' => 'Pengajuan dukungan untuk mengikuti kompetisi pengembangan aplikasi tingkat nasional.',
                'participation' => 'Tim',
                'members' => [['name' => 'Arya Bima', 'role' => 'Ketua'], ['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota']],
                'level' => 'Nasional',
            ],
            [
                'status' => 'Selesai',
                'submitted_at' => '2026-03-21T13:15:00+07:00',
                'category' => 'Lomba',
                'title' => 'Program Kreativitas Mahasiswa bidang Karsa Cipta',
                'organizer' => 'Direktorat Pembelajaran dan Kemahasiswaan',
                'location' => 'Semarang',
                'start_date' => '2026-05-04',
                'end_date' => '2026-05-06',
                'description' => 'Pengembangan prototipe perangkat pemantau kualitas udara berbasis IoT.',
                'participation' => 'Tim',
                'members' => [['name' => 'Arya Bima', 'role' => 'Ketua'], ['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota']],
                'level' => 'Nasional',
            ],
            [
                'status' => 'Disetujui',
                'submitted_at' => '2026-04-08T08:45:00+07:00',
                'category' => 'Lainnya',
                'title' => 'Seminar Nasional Teknologi Terapan 2026',
                'organizer' => 'Politeknik Negeri Semarang',
                'location' => 'Semarang',
                'start_date' => '2026-04-27',
                'end_date' => '2026-04-27',
                'description' => 'Mengikuti seminar dan mempresentasikan hasil karya teknologi terapan.',
                'scope' => 'Nasional',
                'role' => 'Peserta',
                'participation' => 'Individu',
            ],
            [
                'status' => 'Selesai',
                'submitted_at' => '2026-04-26T14:00:00+07:00',
                'category' => 'Volunteer',
                'title' => 'Relawan Mengajar Digital untuk Pelajar Semarang',
                'organizer' => 'Komunitas Sahabat Pendidikan',
                'location' => 'Semarang',
                'start_date' => '2026-06-06',
                'end_date' => '2026-06-07',
                'description' => 'Berbagi pengenalan komputer dan literasi digital kepada pelajar.',
                'role' => 'Mentor teknologi',
            ],
            [
                'status' => 'Disetujui',
                'submitted_at' => '2026-05-11T11:20:00+07:00',
                'category' => 'Lomba',
                'title' => 'Lomba Inovasi Teknologi Tepat Guna Jawa Tengah',
                'organizer' => 'Pemerintah Provinsi Jawa Tengah',
                'location' => 'Surakarta',
                'start_date' => '2026-06-20',
                'end_date' => '2026-06-21',
                'description' => 'Kompetisi karya inovasi teknologi tepat guna tingkat provinsi.',
                'participation' => 'Tim',
                'members' => [['name' => 'Arya Bima', 'role' => 'Ketua'], ['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota']],
                'level' => 'Provinsi',
            ],
            [
                'status' => 'Selesai',
                'submitted_at' => '2026-05-29T09:40:00+07:00',
                'category' => 'Magang',
                'title' => 'Magang Software Developer di PT Aplikasi Nusantara',
                'organizer' => 'PT Aplikasi Nusantara',
                'location' => 'Yogyakarta',
                'start_date' => '2026-07-01',
                'end_date' => '2026-09-30',
                'description' => 'Program magang pengembangan aplikasi web selama tiga bulan.',
                'role' => 'Software Developer Intern',
                'duration_months' => 3,
            ],
            [
                'status' => 'Disetujui',
                'submitted_at' => '2026-06-17T15:10:00+07:00',
                'category' => 'Lainnya',
                'title' => 'Pelatihan Keamanan Jaringan dan Cloud Computing',
                'organizer' => 'Himpunan Mahasiswa Teknologi Rekayasa Komputer',
                'location' => 'Semarang',
                'start_date' => '2026-07-04',
                'end_date' => '2026-07-05',
                'description' => 'Pelatihan peningkatan kompetensi keamanan jaringan dan komputasi awan.',
                'scope' => 'Kampus',
                'role' => 'Peserta',
                'participation' => 'Individu',
            ],
            [
                'status' => 'Direview',
                'submitted_at' => '2026-07-02T10:00:00+07:00',
                'category' => 'Volunteer',
                'title' => 'Aksi Bersih Pantai dan Edukasi Lingkungan',
                'organizer' => 'Komunitas Pesisir Lestari',
                'location' => 'Jepara',
                'start_date' => '2026-08-08',
                'end_date' => '2026-08-09',
                'description' => 'Kegiatan relawan untuk membersihkan kawasan pantai dan mengedukasi warga.',
                'role' => 'Koordinator dokumentasi',
            ],
            [
                'status' => 'Diajukan',
                'submitted_at' => '2026-07-20T08:30:00+07:00',
                'category' => 'Lomba',
                'title' => 'Kompetisi UI/UX Design Festival Mahasiswa 2026',
                'organizer' => 'Asosiasi Pendidikan Tinggi Informatika dan Komputer',
                'location' => 'Bandung',
                'start_date' => '2026-09-12',
                'end_date' => '2026-09-13',
                'description' => 'Kompetisi perancangan antarmuka dan pengalaman pengguna aplikasi layanan publik.',
                'participation' => 'Tim',
                'members' => [['name' => 'Arya Bima', 'role' => 'Ketua'], ['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota']],
                'level' => 'Nasional',
            ],
            [
                'status' => 'Perlu revisi',
                'submitted_at' => '2026-08-14T13:25:00+07:00',
                'category' => 'Magang',
                'title' => 'Magang Network Engineer di PT Digital Solusi',
                'organizer' => 'PT Digital Solusi',
                'location' => 'Semarang',
                'start_date' => '2026-10-01',
                'end_date' => '2026-12-31',
                'description' => 'Program praktik kerja lapangan pada tim infrastruktur jaringan.',
                'role' => 'Network Engineer Intern',
                'duration_months' => 3,
            ],
            [
                'status' => 'Direview',
                'submitted_at' => '2026-09-05T16:05:00+07:00',
                'category' => 'Lomba',
                'title' => 'Kompetisi Robotika Politeknik Indonesia 2026',
                'organizer' => 'Badan Koordinasi Kemahasiswaan Politeknik Indonesia',
                'location' => 'Surabaya',
                'start_date' => '2026-10-10',
                'end_date' => '2026-10-12',
                'description' => 'Kompetisi robotika untuk mahasiswa politeknik tingkat nasional.',
                'participation' => 'Tim',
                'members' => [['name' => 'Arya Bima', 'role' => 'Ketua'], ['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota']],
                'level' => 'Nasional',
            ],
            [
                'status' => 'Ditolak',
                'submitted_at' => '2026-09-18T09:20:00+07:00',
                'category' => 'Volunteer',
                'title' => 'Relawan Literasi Digital untuk UMKM',
                'organizer' => 'Dinas Koperasi dan UMKM Kota Semarang',
                'location' => 'Semarang',
                'start_date' => '2026-10-03',
                'end_date' => '2026-10-04',
                'description' => 'Pendampingan penggunaan layanan digital bagi pelaku UMKM lokal.',
                'role' => 'Pendamping teknologi',
            ],
            [
                'status' => 'Diajukan',
                'submitted_at' => '2026-09-27T13:40:00+07:00',
                'category' => 'Lomba',
                'title' => 'National Cyber Security Competition 2026',
                'organizer' => 'Asosiasi Cyber Security Indonesia',
                'location' => 'Jakarta',
                'start_date' => '2026-11-07',
                'end_date' => '2026-11-08',
                'description' => 'Kompetisi keamanan siber dengan kategori capture the flag.',
                'participation' => 'Tim',
                'members' => [['name' => 'Arya Bima', 'role' => 'Ketua'], ['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota']],
                'level' => 'Nasional',
            ],
            [
                'status' => 'Direview',
                'submitted_at' => '2026-10-04T10:15:00+07:00',
                'category' => 'Lainnya',
                'title' => 'Workshop Pengembangan Aplikasi Berbasis Cloud',
                'organizer' => 'Cloud Computing Indonesia',
                'location' => 'Daring',
                'start_date' => '2026-10-29',
                'end_date' => '2026-10-30',
                'description' => 'Workshop praktik membangun dan menerapkan aplikasi pada layanan cloud.',
                'scope' => 'Nasional',
                'role' => 'Peserta',
                'participation' => 'Individu',
            ],
            [
                'status' => 'Perlu revisi',
                'submitted_at' => '2026-10-13T14:50:00+07:00',
                'category' => 'Magang',
                'title' => 'Magang UI/UX Designer di PT Kreasi Digital',
                'organizer' => 'PT Kreasi Digital',
                'location' => 'Semarang',
                'start_date' => '2026-12-01',
                'end_date' => '2027-02-28',
                'description' => 'Program magang pada tim desain produk digital selama tiga bulan.',
                'role' => 'UI/UX Designer Intern',
                'duration_months' => 3,
            ],
            [
                'status' => 'Direview',
                'submitted_at' => '2026-10-22T08:55:00+07:00',
                'category' => 'Lainnya',
                'title' => 'Seminar Nasional Transformasi Digital Kampus',
                'organizer' => 'Forum Pendidikan Tinggi Indonesia',
                'location' => 'Surakarta',
                'start_date' => '2026-11-14',
                'end_date' => '2026-11-14',
                'description' => 'Seminar tentang penerapan teknologi digital untuk meningkatkan layanan kampus.',
                'scope' => 'Nasional',
                'role' => 'Narasumber',
                'participation' => 'Individu',
            ],
        ];

        return collect($entries)->map(function ($entry, $i) {
            $data = $entry;
            unset($data['status'], $data['submitted_at']);

            return [
                'id' => $i + 1,
                'title' => $entry['title'],
                'category' => $entry['category'],
                'type' => $this->typeFor($entry['category']),
                'status' => $entry['status'],
                'submitted_at' => $entry['submitted_at'],
                'data' => $data,
                'documents' => null,
            ];
        });
    }

    private function all(): Collection
    {
        $mine = collect($this->items())->keyBy('id');

        return $this->dummyItems()->keyBy('id')->replace($mine)->values();
    }

    private function find(int $id): array
    {
        $item = $this->all()->firstWhere('id', $id);
        abort_if(! $item, 404);

        return $item;
    }

    private function canEdit(array $item): bool
    {
        return in_array($item['status'], self::EDITABLE, true);
    }

    private function findEditable(int $id): array
    {
        $item = $this->find($id);
        abort_unless($this->canEdit($item), 403, 'Pengajuan dengan status '.$item['status'].' tidak dapat diedit.');

        return $item;
    }

    private function toRow(array $i): array
    {
        return [
            'id' => $i['id'],
            'title' => $i['title'],
            'category' => $i['category'],
            'type' => $i['type'],
            'status' => $i['status'],
            'submitted_at' => Carbon::parse($i['submitted_at']),
            'editable' => $this->canEdit($i),
        ];
    }

    private function rows(): Collection
    {
        return $this->all()->map(fn ($i) => $this->toRow($i))
            ->sortByDesc(fn ($r) => $r['submitted_at']->timestamp * 1000 + $r['id'])
            ->values();
    }

    private function readableSize(int $b): string
    {
        if ($b < 1024) {
            return $b.' B';
        }

        return $b < 1048576 ? round($b / 1024).' KB' : number_format($b / 1048576, 1, ',', '.').' MB';
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

            return (! $status || $r['status'] === $status)
                && (! $q || str_contains(mb_strtolower($r['title'].' '.$r['category']), mb_strtolower($q)))
                && (! $from || $date >= $from)
                && (! $to || $date <= $to);
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

        return view('dosen.pengajuan.index', compact('rows', 'all', 'counts', 'q', 'status', 'from', 'to'));
    }

    public function create()
    {
        return view('dosen.pengajuan.create', ['item' => null]);
    }

    public function edit(int $id)
    {
        return view('dosen.pengajuan.create', ['item' => $this->findEditable($id)]);
    }

    public function show(int $id)
    {
        $item = $this->find($id);
        $row = $this->toRow($item);

        $flows = [
            'Diajukan' => ['Diajukan'],
            'Direview' => ['Diajukan', 'Direview'],
            'Disetujui' => ['Diajukan', 'Direview', 'Diterima'],
            'Selesai' => ['Diajukan', 'Direview', 'Diterima', 'Selesai'],
            'Ditolak' => ['Diajukan', 'Direview', 'Ditolak'],
            'Perlu revisi' => ['Diajukan', 'Direview', 'Direvisi'],
        ];

        $d = $row['submitted_at'];
        $mine = $id > 100;
        $meta = [
            'Diajukan' => [$mine ? $d : $d->copy()->setTime(9, 0), 'Arya Bima', 'Pengajuan dikirim untuk ditinjau.'],
            'Direview' => [$mine ? $d->copy()->addMinutes(5) : $d->copy()->setTime(13, 0), 'Tim administrasi', 'Verifikasi persyaratan dan periode pengajuan.'],
            'Diterima' => [$d->copy()->addDay()->setTime(10, 15), 'Reviewer akademik', 'Pengajuan disetujui. Menunggu penerbitan dokumen hasil.'],
            'Ditolak' => [$d->copy()->addDay()->setTime(10, 30), 'Reviewer akademik', 'Pengajuan di luar periode penerimaan. Proses dihentikan.'],
            'Direvisi' => [$d->copy()->addDay()->setTime(10, 0), 'Reviewer akademik', 'Rincian anggaran dan lembar pengesahan perlu diperbaiki.'],
            'Selesai' => [$d->copy()->addDays(2)->setTime(14, 0), 'Tim administrasi', 'Dokumen diterbitkan dan proses pengajuan ditutup.'],
        ];

        $steps = collect($flows[$row['status']])->map(fn ($s) => [
            'title' => $s, 'at' => $meta[$s][0], 'actor' => $meta[$s][1], 'note' => $meta[$s][2],
        ]);

        $student = ['name' => 'Arya Bima', 'nim' => '43325115', 'prodi' => 'Teknologi Rekayasa Komputer'];

        $files = $item['documents']
            ? collect($item['documents'])->map(fn ($f) => [
                'name' => $f['name'],
                'meta' => strtoupper(pathinfo($f['name'], PATHINFO_EXTENSION)).' • '.$this->readableSize((int) $f['size']),
                'url' => asset('storage/'.$f['path']),
            ])->all()
            : [
                ['name' => 'Dokumen_pengajuan_Arya.pdf', 'meta' => 'PDF • 1,2 MB'],
                ['name' => 'Lampiran_pendukung.pdf', 'meta' => 'PDF • 840 KB'],
            ];

        return view('mahasiswa.PengajuanMahasiswa.detail', compact('row', 'steps', 'student', 'files', 'item'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip', 'max:10240'],
        ], [
            'file.mimes' => 'Format tidak didukung.',
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
            'file.uploaded' => 'Gagal mengunggah. Ukuran melebihi batas server (cek upload_max_filesize di php.ini).',
        ]);

        $file = $request->file('file');
        $path = $file->store('tmp', 'public');

        return response()->json([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);
    }

    public function removeUpload(Request $request)
    {
        $path = $request->input('path');

        if (is_string($path) && str_starts_with($path, 'tmp/') && ! str_contains($path, '..')) {
            Storage::disk('public')->delete($path);
        }

        return response()->json(['ok' => true]);
    }

    private function validated(Request $request, array $oldDocs = [], bool $docsOptional = false): array
    {
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
            'documents' => $docsOptional ? ['nullable', 'array', 'max:5'] : ['required', 'array', 'min:1', 'max:5'],
            'documents.*' => ['required', 'string', function ($attr, $value, $fail) use ($oldDocs) {
                $valid = (str_starts_with($value, 'tmp/') || in_array($value, $oldDocs, true))
                    && ! str_contains($value, '..')
                    && Storage::disk('public')->exists($value);

                if (! $valid) {
                    $fail('Berkas tidak valid, silakan unggah ulang.');
                }
            }],
            'document_names' => ['nullable', 'array'],
            'document_names.*' => ['nullable', 'string', 'max:255'],
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
            'Lainnya' => [
                'title' => ['required', 'string', 'max:255'],
                'organizer' => ['required', 'string', 'max:255'],
                'scope' => ['required', Rule::in(['Kampus', 'Regional', 'Nasional', 'Internasional'])],
                'role' => ['required', Rule::in(['Peserta', 'Panitia', 'Narasumber', 'Lainnya'])],
                'participation' => ['required', Rule::in(['Individu', 'Tim'])],
                'members' => $members,
                'info_url' => ['nullable', 'url'],
                'advisor' => ['nullable', 'string', 'max:255'],
            ],
            default => [],
        };

        $data = $request->validate($common + $specific);

        if ($data['category'] === 'Magang') {
            $data['title'] = 'Magang '.$data['role'].' di '.$data['organizer'];
        }

        if (isset($data['members'])) {
            $data['members'] = json_decode($data['members'], true);
        }

        return $data;
    }

    private function persistDocuments(array $data): array
    {
        return collect($data['documents'] ?? [])->map(function ($path, $i) use ($data) {
            $name = $data['document_names'][$i] ?? basename($path);

            if (str_starts_with($path, 'tmp/')) {
                $final = 'pengajuan/'.basename($path);
                Storage::disk('public')->move($path, $final);
                $path = $final;
            }

            return ['path' => $path, 'name' => $name, 'size' => Storage::disk('public')->size($path)];
        })->values()->all();
    }

    private function typeFor(string $category): string
    {
        return $category === 'Lainnya' ? 'Kegiatan mahasiswa' : 'Dukungan '.strtolower($category).' mahasiswa';
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $files = $this->persistDocuments($data);
        unset($data['documents'], $data['document_names']);

        $items = $this->items();
        $id = max(100, (int) collect($items)->max('id')) + 1;

        $items[] = [
            'id' => $id,
            'title' => $data['title'],
            'category' => $data['category'],
            'type' => $this->typeFor($data['category']),
            'status' => 'Diajukan',
            'submitted_at' => now()->toIso8601String(),
            'data' => $data,
            'documents' => $files,
        ];
        session([self::KEY => $items]);

        // TODO: ganti penyimpanan session dengan Eloquent (Submission::create) saat database siap.

        return redirect()->route('pengajuan.show', $id)
            ->with('success', 'Pengajuan berhasil dikirim.')
            ->with('preview', true);
    }

    public function update(Request $request, int $id)
    {
        $item = $this->findEditable($id);
        $legacy = $item['documents'] === null;
        $oldPaths = collect($item['documents'] ?? [])->pluck('path')->all();

        $data = $this->validated($request, $oldPaths, $legacy);
        $files = $this->persistDocuments($data);
        unset($data['documents'], $data['document_names']);

        foreach (array_diff($oldPaths, collect($files)->pluck('path')->all()) as $removed) {
            Storage::disk('public')->delete($removed);
        }

        $updated = array_merge($item, [
            'title' => $data['title'],
            'category' => $data['category'],
            'type' => $this->typeFor($data['category']),
            'data' => $data,
            'documents' => ($legacy && ! $files) ? null : $files,
            'updated_at' => now()->toIso8601String(),
        ]);

        $items = collect($this->items())->reject(fn ($i) => $i['id'] === $id)->push($updated)->values()->all();
        session([self::KEY => $items]);

        return redirect()->route('pengajuan.show', $id)
            ->with('success', 'Perubahan pengajuan berhasil disimpan.')
            ->with('preview', true);
    }
}
