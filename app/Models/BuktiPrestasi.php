<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiPrestasi extends Model
{
    protected $table = 'bukti_prestasi';

    protected $primaryKey = 'id_bukti_prestasi';

    protected $fillable = ['id_prestasi', 'id_jenis_dokumen', 'link', 'nama_file'];

    public function prestasi(): BelongsTo
    {
        return $this->belongsTo(Prestasi::class, 'id_prestasi', 'id_prestasi');
    }

    public function jenisDokumen(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class, 'id_jenis_dokumen', 'id_jenis_dokumen');
    }
}
