<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajaran extends Model
{
    protected $table = 'pengajaran';

    protected $primaryKey = 'id_pengajaran';

    protected $fillable = ['id_enrollment_kelas_mk', 'id_dokumen'];

    public function enrollmentKelasMataKuliah(): BelongsTo
    {
        return $this->belongsTo(EnrollmentKelasMataKuliah::class, 'id_enrollment_kelas_mk', 'id_enrollment_kelas_mk');
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'id_dokumen', 'id_dokumen');
    }
}
