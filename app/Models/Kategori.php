<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $primaryKey = 'id_kategori';
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;
>>>>>>> arinda-dokumen-histori

    protected $fillable = [
        'id_jenis_kegiatan',
        'nama_kategori',
        'deskripsi',
    ];

<<<<<<< HEAD
    public function jenisKegiatan(): BelongsTo
=======
    public function jenisKegiatan()
>>>>>>> arinda-dokumen-histori
    {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }

<<<<<<< HEAD
    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'id_kategori', 'id_kategori');
    }
}
=======
    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'id_kategori', 'id_kategori');
    }
}
>>>>>>> arinda-dokumen-histori
