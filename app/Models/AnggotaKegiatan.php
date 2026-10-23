<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaKegiatan extends Model
{
    protected $table = 'anggota_kegiatan';

    protected $primaryKey = 'id_anggota_kegiatan';

    protected $fillable = ['id_kegiatan', 'id_user', 'peran'];
}
