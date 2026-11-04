<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'id_jenis_kegiatan', 'nama_kategori', 'deskripsi',
        'template_surat', 'lampiran_peserta',
    ];

    protected function casts(): array
    {
        return [
            'lampiran_peserta' => 'boolean',
        ];
    }

    public function jenisKegiatan(): BelongsTo
    {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan');
    }
}
