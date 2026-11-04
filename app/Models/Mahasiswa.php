<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $primaryKey = 'id_mahasiswa';

    protected $fillable = [
        'id_user',
        'nim',
        'nama',
        'tahun_masuk',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function enrollmentTingkatKelas(): HasMany
    {
        return $this->hasMany(EnrollmentMahasiswaTingkatKelas::class, 'id_mahasiswa', 'id_mahasiswa');
    }
}
