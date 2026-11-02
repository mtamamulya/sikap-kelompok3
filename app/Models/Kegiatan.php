<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'id_kategori', 'id_user', 'id_kegiatan_sebelumnya',
        'nama_kegiatan', 'lokasi', 'deskripsi', 'nomor_sk',
        'tanggal_sk', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function statusTerkini(): HasOne
    {
        return $this->hasOne(Timeline::class, 'id_kegiatan', 'id_kegiatan')
            ->latestOfMany('tanggal_status');
    }

    public function scopeUntukUser(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(fn (Builder $q) => $q->where('id_user', $user->getKey())
            ->orWhereHas('anggota', fn (Builder $q) => $q->where('id_user', $user->getKey())));
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(DetailKegiatan::class, 'id_kegiatan');
    }

    public function prestasi(): HasOne
    {
        return $this->hasOne(Prestasi::class, 'id_kegiatan');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'id_kegiatan')->orderBy('id_dokumen');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(AnggotaKegiatan::class, 'id_kegiatan');
    }
}
