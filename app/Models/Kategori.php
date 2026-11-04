<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'id_jenis_kegiatan', 'nama_kategori', 'deskripsi',
        'template_surat', 'isi_surat', 'lampiran_peserta', 'kebutuhan',
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

    protected function isiSurat(): Attribute
    {
        // ERD name mapped onto the existing physical column, without duplicate content.
        return Attribute::make(
            get: fn () => $this->template_surat,
            set: fn ($value) => ['template_surat' => $value],
        );
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
