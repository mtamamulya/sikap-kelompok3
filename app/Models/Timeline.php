<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function buktiHistory(): HasMany
    {
        return $this->hasMany(BuktiHistory::class, 'id_timeline', 'id_timeline');
    }
}
