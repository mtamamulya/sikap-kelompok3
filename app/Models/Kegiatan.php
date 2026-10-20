<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'id_kategori',
        'id_user',
    ];

    // Relasi
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detailKegiatan()
    {
        return $this->hasOne(DetailKegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function berita()
    {
        return $this->hasOne(Berita::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function anggotaKegiatan()
    {
        return $this->hasMany(AnggotaKegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}
