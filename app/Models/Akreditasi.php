<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Akreditasi extends Model
{
    protected $table = 'akreditasi';

    protected $primaryKey = 'id_akreditasi';

    protected $fillable = [
        'id_user',
        'keterangan',
        'link',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
