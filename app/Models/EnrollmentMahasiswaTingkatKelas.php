<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentMahasiswaTingkatKelas extends Model
{
    protected $table = 'enrollment_mahasiswa_tingkat_kelas';

    protected $primaryKey = 'id_enrollment_mahasiswa_tingkat_kelas';

    protected $fillable = [
        'id_enrollment_tingkat_kelas',
        'id_mahasiswa',
    ];

    public function enrollmentTingkatKelas(): BelongsTo
    {
        return $this->belongsTo(
            EnrollmentTingkatKelas::class,
            'id_enrollment_tingkat_kelas',
            'id_enrollment_tingkat_kelas'
        );
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }
}
