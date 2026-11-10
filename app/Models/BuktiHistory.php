<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class BuktiHistory extends Model
{
    protected $table = 'bukti_history';

    protected $primaryKey = 'id_bukti_history';

    protected $fillable = [
        'id_dokumen',
        'id_timeline',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $proof) {
            $document = Dokumen::find($proof->id_dokumen);
            $timeline = Timeline::find($proof->id_timeline);
            if (! $document || ! $timeline || $document->id_kegiatan !== $timeline->id_kegiatan) {
                throw ValidationException::withMessages(['id_dokumen' => 'Dokumen bukti harus berasal dari kegiatan timeline yang sama.']);
            }
        });
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'id_dokumen', 'id_dokumen');
    }

    public function timeline(): BelongsTo
    {
        return $this->belongsTo(Timeline::class, 'id_timeline', 'id_timeline');
    }
}
