<?php

namespace App\Repositories;

use App\Models\Kurikulum;
use App\Models\Prodi;
use App\Support\KolomOpsional;
use App\Support\LinkFile;
use Illuminate\Http\UploadedFile;

/**
 * Versi DATABASE dari KurikulumStore (tabel `kurikulum`).
 * Nama method dan bentuk array SAMA dengan versi JSON, jadi controller dan view tidak berubah.
 * Dipakai bila DATA_SOURCE=database (lihat AppServiceProvider).
 *
 * Pemetaan: id <- id_kurikulum, name <- nama_kurikulum, description <- deskripsi*, year <- tahun_terbit*,
 *           status (aktif|nonaktif) <- status (boolean), file <- link.
 * (*) kolom usulan; selama belum ada di database dilewati otomatis (KolomOpsional) dan nilainya kosong.
 */
class KurikulumDbStore extends KurikulumStore
{
    public function all(): array
    {
        return Kurikulum::query()->orderByDesc('id_kurikulum')->get()
            ->map(fn (Kurikulum $k) => $this->baris($k))->all();
    }

    public function find(string $id): ?array
    {
        $k = ctype_digit($id) ? Kurikulum::query()->find((int) $id) : null;

        return $k ? $this->baris($k) : null;
    }

    public function create(array $data, ?UploadedFile $pdf = null): array
    {
        $k = new Kurikulum;
        $k->forceFill(KolomOpsional::saring('kurikulum', [
            'id_prodi' => $this->idProdi(),
            'nama_kurikulum' => $data['name'],
            'deskripsi' => ($data['description'] ?? '') !== '' ? $data['description'] : null,
            'tahun_terbit' => ($data['year'] ?? '') !== '' ? (int) $data['year'] : null,
            'status' => true,
            'link' => $pdf ? $pdf->store('kurikulum', 'public') : null,
        ]))->save();

        return $this->baris($k);
    }

    public function update(string $id, array $data, ?UploadedFile $pdf = null): ?array
    {
        $k = ctype_digit($id) ? Kurikulum::query()->find((int) $id) : null;
        if (! $k) {
            return null;
        }

        $k->forceFill(KolomOpsional::saring('kurikulum', [
            'nama_kurikulum' => $data['name'],
            'deskripsi' => ($data['description'] ?? '') !== '' ? $data['description'] : null,
            'tahun_terbit' => ($data['year'] ?? '') !== '' ? (int) $data['year'] : null,
        ]));
        if ($pdf) {
            LinkFile::hapus($k->link);
            $k->link = $pdf->store('kurikulum', 'public');
        }
        $k->save();

        return $this->baris($k);
    }

    public function toggleStatus(string $id): ?array
    {
        $k = ctype_digit($id) ? Kurikulum::query()->find((int) $id) : null;
        if (! $k) {
            return null;
        }

        $k->status = ! $k->status;
        $k->save();

        return $this->baris($k);
    }

    /** Mata kuliah ikut terhapus oleh database (cascadeOnDelete pada mata_kuliah.id_kurikulum). */
    public function delete(string $id): bool
    {
        $k = ctype_digit($id) ? Kurikulum::query()->find((int) $id) : null;
        if (! $k) {
            return false;
        }

        LinkFile::hapus($k->link);
        $k->delete();

        return true;
    }

    public function pdfUrl(array $row): ?string
    {
        return LinkFile::url($row['file'] ?? null);
    }

    private function baris(Kurikulum $k): array
    {
        return [
            'id' => (string) $k->id_kurikulum,
            'name' => $k->nama_kurikulum,
            'description' => (string) $k->getAttribute('deskripsi'),
            'year' => (string) $k->getAttribute('tahun_terbit'),
            'status' => $k->status ? 'aktif' : 'nonaktif',
            'file' => $k->link,
            'created_at' => $k->created_at?->toDateTimeString(),
        ];
    }

    /**
     * kurikulum.id_prodi wajib diisi. Aplikasi ini hanya untuk satu prodi (TRK), jadi dipakai baris prodi pertama.
     * Bila tabel prodi masih kosong, satu baris TRK dibuat otomatis dari config('admin.prodi').
     */
    private function idProdi(): int
    {
        $id = Prodi::query()->orderBy('id_prodi')->value('id_prodi');
        if ($id) {
            return (int) $id;
        }

        $p = config('admin.prodi', []);

        return (int) Prodi::query()->create([
            'kode_prodi' => $p['kode'] ?? 'TRK',
            'nama_prodi' => $p['nama'] ?? 'Teknologi Rekayasa Komputer',
            'jenjang' => $p['jenjang'] ?? 'D4',
        ])->getKey();
    }
}
