<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prestasi extends Model
{
    protected $table = 'prestasi';

    protected $primaryKey = 'id_prestasi';

    protected $fillable = [
        'id_kegiatan',
        'jenis_prestasi',
        'bidang_lomba',
        'catatan',
        'capaian',
        'form_data',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
        ];
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function buktiPrestasi(): HasMany
    {
        return $this->hasMany(BuktiPrestasi::class, 'id_prestasi', 'id_prestasi');
    }
}
