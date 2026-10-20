<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jenis_kegiatan';
    protected $primaryKey = 'id_jenis_kegiatan';
    public $timestamps = false;

    protected $fillable = [
        'nama_jenis_kegiatan',
    ];

    public function kategori()
    {
        return $this->hasMany(Kategori::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }
}