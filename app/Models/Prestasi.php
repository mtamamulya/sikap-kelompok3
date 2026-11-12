<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prestasi extends Model
{
    protected $table = 'prestasi';

    protected $primaryKey = 'id_prestasi';

    protected $fillable = [
        'id_user', 
        'created_by', 
        'id_kegiatan', 
        'capaian', 
        'jenis_prestasi', 
        'bidang_lomba', 
        'nama_lomba', 
        'penyelenggara', 
        'lokasi', 
        'dosen_pembimbing', 
        'tingkat_kejuaraan', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'bentuk_partisipasi', 
        'deskripsi', 
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function buktiPrestasi(): HasMany
    {
        return $this->hasMany(BuktiPrestasi::class, 'id_prestasi', 'id_prestasi');
    }
}