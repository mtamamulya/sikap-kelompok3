<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

    protected $table = 'timeline';
    protected $primaryKey = 'id_timeline';
    public $timestamps = false;

    protected $fillable = [
        'id_kegiatan',
        'id_kegiatan_dosen',
        'status',
        'tanggal_status',
        'catatan_revisi',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function kegiatanDosen()
    {
        return $this->belongsTo(KegiatanDosen::class, 'id_kegiatan_dosen', 'id_kegiatan_dosen');
    }
}