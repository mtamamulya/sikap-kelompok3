<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skim extends Model
{
    protected $table = 'skim';

    protected $primaryKey = 'id_skim';

    protected $fillable = ['nama_skim', 'ranah'];

    public function kegiatanDosen(): HasMany
    {
        return $this->hasMany(KegiatanDosen::class, 'id_skim', 'id_skim');
    }
}
