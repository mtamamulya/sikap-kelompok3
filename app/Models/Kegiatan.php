<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';
    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'id_user',
        'id_kategori',
        'id_kegiatan_sebelumnya',
        'nama_kegiatan',
        'lokasi',
        'deskripsi',
        'nomor_sk',
        'tanggal_sk',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function scopeUntukUser($query, $user)
    {
        return $query->where('id_user', $user?->id_user ?? $user?->id);
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function statusTerkini()
    {
        return $this->hasOne(DetailKegiatan::class, 'id_kegiatan', 'id_kegiatan')
                    ->latestOfMany('id_detail_kegiatan');
    }
}