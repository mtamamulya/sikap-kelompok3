<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisKegiatan extends Model
{
    protected $table = 'jenis_kegiatan';

    protected $primaryKey = 'id_jenis_kegiatan';

    protected $fillable = [
        'nama_jenis_kegiatan',
    ];

    public function kategori(): HasMany
    {
        return $this->hasMany(Kategori::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }
}
