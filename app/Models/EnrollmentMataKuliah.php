<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentMataKuliah extends Model
{
    protected $table = 'enrollment_mata_kuliah';

    protected $primaryKey = 'id_enrollment_mk';

    protected $fillable = [
        'id_mk',
        'id_tahun_akademik',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mk', 'id_mk');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik', 'id_tahun_akademik');
    }
}
