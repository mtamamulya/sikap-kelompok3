<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKegiatan extends Model
{
    protected $table = 'anggota_kegiatan';

    protected $primaryKey = 'id_anggota_kegiatan';

    protected $fillable = ['id_kegiatan', 'id_user', 'peran'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}
