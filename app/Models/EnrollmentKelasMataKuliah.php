<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnrollmentKelasMataKuliah extends Model
{
    protected $table = 'enrollment_kelas_mata_kuliah';

    protected $primaryKey = 'id_enrollment_kelas_mk';

    protected $fillable = [
        'id_enrollment_tingkat_kelas',
        'id_enrollment_mk',
        'id_dosen',
    ];

    public function enrollmentTingkatKelas(): BelongsTo
    {
        return $this->belongsTo(EnrollmentTingkatKelas::class, 'id_enrollment_tingkat_kelas', 'id_enrollment_tingkat_kelas');
    }

    public function enrollmentMataKuliah(): BelongsTo
    {
        return $this->belongsTo(EnrollmentMataKuliah::class, 'id_enrollment_mk', 'id_enrollment_mk');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }

    public function pengajaran(): HasMany
    {
        return $this->hasMany(Pengajaran::class, 'id_enrollment_kelas_mk', 'id_enrollment_kelas_mk');
    }
}
