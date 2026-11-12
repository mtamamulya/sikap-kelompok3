<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class KelompokBidang extends Model
{
    protected $table = 'kelompok_bidang';

    protected $primaryKey = 'id_kelompok_bidang';

    protected $fillable = ['id_rujukan', 'nama_kelompok_bidang'];

    protected static function booted(): void
    {
        static::saving(function (self $bidang) {
            if ($bidang->exists && $bidang->id_rujukan !== null
                && (int) $bidang->id_rujukan === (int) $bidang->getKey()) {
                throw ValidationException::withMessages(['id_rujukan' => 'Kelompok bidang tidak boleh merujuk ke dirinya sendiri.']);
            }
        });
    }

    public function rujukan(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id_rujukan', 'id_kelompok_bidang');
    }

    public function turunan(): HasMany
    {
        return $this->hasMany(self::class, 'id_rujukan', 'id_kelompok_bidang');
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'id_kelompok_bidang', 'id_kelompok_bidang');
    }
}