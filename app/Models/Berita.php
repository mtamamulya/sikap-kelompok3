<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Berita extends Model
{
    protected $table = 'berita';

    protected $primaryKey = 'id_berita';

    protected $fillable = [
        'id_kegiatan',
        'judul_berita',
        'isi_berita',
        'status',
        'tanggal_publish',
    ];

    protected $casts = [
        'tanggal_publish' => 'datetime',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}
