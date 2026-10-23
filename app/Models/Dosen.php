<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    protected $table = 'dosen';

    protected $primaryKey = 'id_dosen';

    protected $fillable = [
        'id_user',
        'nip',
        'nama',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function historiPangkatGolongan(): HasMany
    {
        return $this->hasMany(
            HistoriPangkatGolongan::class,
            'id_dosen',
            'id_dosen'
        );
    }
}
