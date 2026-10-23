<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKegiatan extends Model
{
    protected $table = 'detail_kegiatan';

    protected $primaryKey = 'id_kegiatan';

    public $incrementing = false;

    protected $fillable = [
        'id_kegiatan', 'id_dosen', 'tingkat_kejuaraan',
        'penyelenggara', 'link_website', 'posisi_dalam_kegiatan',
    ];
}
