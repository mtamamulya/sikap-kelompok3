<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'id_jenis_kegiatan', 'nama_kategori', 'deskripsi',
        'isi_surat', 'lampiran_peserta', 'kebutuhan',
    ];

    protected function casts(): array
    {
        return [
            'lampiran_peserta' => 'boolean',
        ];
    }

    public function jenisKegiatan(): BelongsTo
    {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }

    public function isianKategori(): HasMany
    {
        return $this->hasMany(IsianKategori::class, 'id_kategori', 'id_kategori');
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'id_kategori', 'id_kategori');
    }
}