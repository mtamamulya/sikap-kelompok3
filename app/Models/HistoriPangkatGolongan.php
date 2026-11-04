<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriPangkatGolongan extends Model
{
    protected $table = 'histori_pangkat_golongan';

    protected $primaryKey = 'id_histori_pangkat_golongan';

    protected $fillable = [
        'id_dosen',
        'pangkat',
        'golongan',
        'nomor_sk',
        'tanggal_sk',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
        ];
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }
}
