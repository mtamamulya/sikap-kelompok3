<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaKegiatan extends Model
{
    use HasFactory;

    protected $table = 'anggota_kegiatan';

    protected $primaryKey = 'id_anggota_kegiatan';

    protected $fillable = [
        'id_user',
        'id_kegiatan',
        'id_kegiatan_dosen',
        'peran',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function kegiatanDosen()
    {
        return $this->belongsTo(KegiatanDosen::class, 'id_kegiatan_dosen', 'id_kegiatan_dosen');
    }
}
