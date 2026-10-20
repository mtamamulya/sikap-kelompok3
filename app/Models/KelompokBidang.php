<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokBidang extends Model
{
    protected $table = 'kelompok_bidang';

    protected $primaryKey = 'id_kelompok_bidang';

    protected $fillable = [
        'nama_kelompok_bidang',
    ];
}
