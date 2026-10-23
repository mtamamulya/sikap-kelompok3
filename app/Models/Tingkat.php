<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tingkat extends Model
{
    protected $table = 'tingkat';

    protected $primaryKey = 'id_tingkat';

    protected $fillable = [
        'tingkat',
        'keterangan',
    ];

    public function enrollmentTingkatKelas(): HasMany
    {
        return $this->hasMany(EnrollmentTingkatKelas::class, 'id_tingkat', 'id_tingkat');
    }
}
