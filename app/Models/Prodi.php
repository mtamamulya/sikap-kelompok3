<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $table = 'prodi';

    protected $primaryKey = 'id_prodi';

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'jenjang',
    ];

    public function kurikulum(): HasMany
    {
        return $this->hasMany(Kurikulum::class, 'id_prodi', 'id_prodi');
    }
}
