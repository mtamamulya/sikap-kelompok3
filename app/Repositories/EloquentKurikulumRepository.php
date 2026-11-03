<?php

namespace App\Repositories;

use App\Models\Kurikulum;
use App\Support\LinkFile;
use Illuminate\Support\Str;

/**
 * Sumber data halaman PUBLIK (tab Kurikulum + menu navbar) dari DATABASE:
 * hanya kurikulum berstatus aktif yang sudah punya PDF, tahun terbit terbaru dulu.
 * Dipakai bila DATA_SOURCE=database (lihat AppServiceProvider). Tabel: kurikulum.
 */
class EloquentKurikulumRepository implements KurikulumRepository
{
    public function all(): array
    {
        return Kurikulum::query()
            ->where('status', true)
            ->whereNotNull('link')
            ->get()
            ->sortByDesc(fn (Kurikulum $k) => [(string) $k->getAttribute('tahun_terbit'), $k->id_kurikulum])
            ->map(fn (Kurikulum $k) => [
                'id' => (string) $k->id_kurikulum,
                'nama' => $k->nama_kurikulum,
                'pdf_url' => LinkFile::url($k->link),
                'file_name' => Str::slug($k->nama_kurikulum).'.pdf',
            ])
            ->values()
            ->all();
    }
}
