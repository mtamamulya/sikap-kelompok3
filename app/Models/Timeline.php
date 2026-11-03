<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
