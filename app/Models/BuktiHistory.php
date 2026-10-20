<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiHistory extends Model
{
    protected $table = 'bukti_history';
    protected $primaryKey = 'id_bukti_history';

    protected $fillable = [
        'id_dokumen',
        'id_timeline',
    ];

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'id_dokumen', 'id_dokumen');
    }

    public function timeline(): BelongsTo
    {
        return $this->belongsTo(Timeline::class, 'id_timeline', 'id_timeline');
    }
}