<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    protected $table = 'timeline';

    protected $primaryKey = 'id_timeline';

    protected $fillable = ['id_kegiatan', 'status', 'tanggal_status', 'catatan_revisi'];

    protected function casts(): array
    {
        return [
            'tanggal_status' => 'datetime',
        ];
    }
}
