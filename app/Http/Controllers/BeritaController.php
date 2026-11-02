<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BeritaController extends Controller
{
    private const KEY = 'pengajuan.berita';

    private const CATEGORIES = [
        'Prestasi Mahasiswa',
        'Prestasi Dosen',
        'Kunjungan Industri',
        'Info Akademik',
        'Lomba',
        'Magang',
    ];

    private function items(): Collection
    {
        return collect(session(self::KEY, []))->sortByDesc('submitted_at')->values();
    }

    private function find(int $id): array
    {
        $item = $this->items()->firstWhere('id', $id);
        abort_if(! $item, 404);

        return $item;
    }

    private function formData(?array $item = null): array
    {
        return [
            'item' => $item,
            'categories' => self::CATEGORIES,
            'activities' => app(PengajuanController::class)->activityOptions(),
        ];
    }

    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $query = trim($request->string('q')->toString());
        $items = $this->items()->when($status, fn (Collection $items) => $items->where('status', $status))
            ->when($query, fn (Collection $items) => $items->filter(fn ($item) => str_contains(
                mb_strtolower($item['title'].' '.$item['category']),
                mb_strtolower($query)
            )))
            ->values();

        return view('PengajuanMahasiswa.berita.index', [
            'items' => $items,
            'status' => $status,
            'query' => $query,
            'counts' => $this->items()->countBy('status'),
        ]);
    }

    public function create()
    {
        return view('PengajuanMahasiswa.berita.form', $this->formData());
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function show(int $id)
    {
        return view('PengajuanMahasiswa.berita.show', ['item' => $this->find($id)]);
    }

    public function edit(int $id)
    {
        $item = $this->find($id);
        abort_unless(in_array($item['status'], ['Draft', 'Direvisi'], true), 403);

        return view('PengajuanMahasiswa.berita.form', $this->formData($item));
    }

    public function update(Request $request, int $id)
    {
        $item = $this->find($id);
        abort_unless(in_array($item['status'], ['Draft', 'Direvisi'], true), 403);

        return $this->save($request, $item);
    }

    private function save(Request $request, ?array $item = null)
    {
        $isSubmit = $request->input('intent') === 'submit';
        $required = $isSubmit ? 'required' : 'nullable';
        $activities = app(PengajuanController::class)->activityOptions()
            ->pluck('title')
            ->all();
        $data = $request->validate([
            'intent' => ['required', Rule::in(['draft', 'submit'])],
            'source_type' => [$required, Rule::in(['previous', 'new'])],
            'previous_activity' => [$isSubmit ? 'required_if:source_type,previous' : 'nullable', 'nullable', 'string', 'max:255', Rule::in($activities)],
            'activity_name' => [$isSubmit ? 'required_if:source_type,new' : 'nullable', 'nullable', 'string', 'max:255'],
            'category' => [$required, Rule::in(self::CATEGORIES)],
            'title' => [$required, 'string', 'max:255'],
            'content' => [$required, 'string', 'max:5000'],
            'cover' => [$isSubmit && ! ($item['cover'] ?? null) ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $items = $this->items();
        $id = $item['id'] ?? ((int) $items->max('id') + 1);
        $coverPath = $item['cover'] ?? null;

        if ($request->hasFile('cover')) {
            $newPath = $request->file('cover')->store('berita', 'public');
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $newPath;
        }

        $saved = [
            'id' => $id,
            'source_type' => $data['source_type'] ?? null,
            'previous_activity' => $data['previous_activity'] ?? null,
            'activity_name' => $data['activity_name'] ?? null,
            'category' => $data['category'] ?? null,
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
            'cover' => $coverPath,
            'status' => $isSubmit ? 'Diajukan' : 'Draft',
            'submitted_at' => now()->toIso8601String(),
        ];

        $items = $items->reject(fn ($entry) => $entry['id'] === $id)->push($saved)->values()->all();
        session([self::KEY => $items]);

        return redirect()->route('pengajuan.berita.show', $id)
            ->with('success', $isSubmit ? 'Berita berhasil diajukan.' : 'Berita berhasil disimpan sebagai draft.');
    }
}
