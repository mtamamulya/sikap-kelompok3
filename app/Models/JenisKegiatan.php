<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisKegiatan extends Model
{
    protected $table = 'jenis_kegiatan';

    protected $primaryKey = 'id_jenis_kegiatan';
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jenis_kegiatan';
    protected $primaryKey = 'id_jenis_kegiatan';
    public $timestamps = false;
>>>>>>> arinda-dokumen-histori

    protected $fillable = [
        'nama_jenis_kegiatan',
    ];

<<<<<<< HEAD
    public function kategori(): HasMany
    {
        return $this->hasMany(Kategori::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }
}
=======
    public function kategori()
    {
        return $this->hasMany(Kategori::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }
}
>>>>>>> arinda-dokumen-histori
