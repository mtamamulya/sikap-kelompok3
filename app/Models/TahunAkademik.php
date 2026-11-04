<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';

    protected $primaryKey = 'id_tahun_akademik';

    protected $fillable = [
        'tahun_akademik',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function enrollmentTingkatKelas(): HasMany
    {
        return $this->hasMany(EnrollmentTingkatKelas::class, 'id_tahun_akademik', 'id_tahun_akademik');
    }

    public function enrollmentMataKuliah(): HasMany
    {
        return $this->hasMany(EnrollmentMataKuliah::class, 'id_tahun_akademik', 'id_tahun_akademik');
    }
}
