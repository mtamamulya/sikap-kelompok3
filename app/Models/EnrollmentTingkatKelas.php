<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnrollmentTingkatKelas extends Model
{
    protected $table = 'enrollment_tingkat_kelas';

    protected $primaryKey = 'id_enrollment_tingkat_kelas';

    protected $fillable = [
        'id_kelas',
        'id_prodi',
        'id_tingkat',
        'id_tahun_akademik',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }

    public function tingkat(): BelongsTo
    {
        return $this->belongsTo(Tingkat::class, 'id_tingkat', 'id_tingkat');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik', 'id_tahun_akademik');
    }

    public function penempatanMahasiswa(): HasMany
    {
        return $this->hasMany(
            EnrollmentMahasiswaTingkatKelas::class,
            'id_enrollment_tingkat_kelas',
            'id_enrollment_tingkat_kelas'
        );
    }
}
