<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'jenis_kelas',
        'keterangan',
    ];

    public function enrollmentTingkatKelas(): HasMany
    {
        return $this->hasMany(EnrollmentTingkatKelas::class, 'id_kelas', 'id_kelas');
    }
}
