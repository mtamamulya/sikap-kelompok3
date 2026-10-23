<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'id_kategori', 'id_user', 'id_kegiatan_sebelumnya',
        'nama_kegiatan', 'lokasi', 'deskripsi', 'nomor_sk',
        'tanggal_sk', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function statusTerkini(): HasOne
    {
        return $this->hasOne(Timeline::class, 'id_kegiatan', 'id_kegiatan')
            ->latestOfMany('tanggal_status');
    }
}
