<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKegiatan extends Model
{
    use HasFactory;

    protected $table = 'detail_kegiatan';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_kegiatan',
        'nama_kegiatan',
        'pembimbing',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'deskripsi',
        'tingkat_kejuaraan',
        'penyelenggara',
        'nomor_surat',
        'link_website',
        'posisi_dalam_kegiatan',
    ];

    // Relasi
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}
