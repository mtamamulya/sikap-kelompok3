<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;

    protected $fillable = [
        'id_jenis_kegiatan',
        'nama_kategori',
        'deskripsi',
    ];

    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'id_kategori', 'id_kategori');
    }
}