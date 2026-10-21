<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'prestasi';
    protected $primaryKey = 'id_prestasi';
    public $timestamps = false;

    protected $fillable = [
        'id_kegiatan',
        'nama_kejuaraan',
        'nama_lomba',
        'penyelenggara',
        'tingkat_kejuaraan',
        'juara_dikelasnya',
        'tanggal_mulai',
        'tanggal_selesai',
        'sertifikat',
        'deskripsi',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}