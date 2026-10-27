<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kurikulum extends Model
{
    protected $table = 'kurikulum';

    protected $primaryKey = 'id_kurikulum';

    protected $fillable = [
        'id_prodi',
        'nama_kurikulum',
        'status',
        'link',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class, 'id_kurikulum', 'id_kurikulum');
    }
}
