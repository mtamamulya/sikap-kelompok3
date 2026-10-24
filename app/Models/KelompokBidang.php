<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokBidang extends Model
{
    protected $table = 'kelompok_bidang';

    protected $primaryKey = 'id_kelompok_bidang';

    protected $fillable = ['nama_kelompok_bidang'];

    public function kegiatanDosen(): HasMany
    {
        return $this->hasMany(KegiatanDosen::class, 'id_kelompok_bidang', 'id_kelompok_bidang');
    }
}
