<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Dokumen extends Model
{
    protected $table = 'dokumen';

    protected $primaryKey = 'id_dokumen';

    protected $fillable = [
        'id_jenis_dokumen',
        'id_kegiatan',
        'nama_file',
        'tipe_dokumen',
        'link_dokumen',
        'is_lampiran',
        'id_detail_kegiatan',
    ];

    protected function casts(): array
    {
        return [
            'is_lampiran' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $document) {
            if ($document->id_detail_kegiatan !== null && ! DetailKegiatan::whereKey($document->id_detail_kegiatan)
                ->where('id_kegiatan', $document->id_kegiatan)->exists()) {
                throw ValidationException::withMessages(['id_detail_kegiatan' => 'Detail dan dokumen harus berasal dari kegiatan yang sama.']);
            }
        });
    }

    public function detailKegiatan(): BelongsTo
    {
        return $this->belongsTo(DetailKegiatan::class, 'id_detail_kegiatan', 'id_detail_kegiatan');
    }

    public function pengajaran(): HasMany
    {
        return $this->hasMany(Pengajaran::class, 'id_dokumen', 'id_dokumen');
    }

    public function jenisDokumen(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class, 'id_jenis_dokumen', 'id_jenis_dokumen');
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function buktiHistory(): HasMany
    {
        return $this->hasMany(BuktiHistory::class, 'id_dokumen', 'id_dokumen');
    }
}