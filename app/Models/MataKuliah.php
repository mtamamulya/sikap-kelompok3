<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $primaryKey = 'id_mk';

    protected $fillable = [
        'id_kurikulum',
        'semester',
        'kode_mk',
        'nama_mk',
        'sks_teori',
        'sks_praktik',
        'jam_teori',
        'jam_praktek',
    ];

    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum', 'id_kurikulum');
    }
}
