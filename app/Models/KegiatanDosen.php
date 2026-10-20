<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KegiatanDosen extends Model
{
    protected $table = 'kegiatan_dosen';

    protected $primaryKey = 'id_kegiatan_dosen';

    protected $fillable = [
        'id_kegiatan',
        'id_skim',
        'id_kelompok_bidang',
        'id_penelitian_sebelumnya',
        'judul',
        'afiliasi',
        'tahun_anggaran',
        'tahun_pelaksanaan_ke',
        'lama_kegiatan_tahun',
        'lokasi',
        'nomor_sk_penugasan',
        'tanggal_sk_penugasan',
        'dana_dikti',
        'dana_pt',
        'dana_institusi_lain',
        'in_kind',
        'mitra',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk_penugasan' => 'date',
            'dana_dikti' => 'decimal:2',
            'dana_pt' => 'decimal:2',
            'dana_institusi_lain' => 'decimal:2',
        ];
    }

    public function skim(): BelongsTo
    {
        return $this->belongsTo(Skim::class, 'id_skim', 'id_skim');
    }

    public function kelompokBidang(): BelongsTo
    {
        return $this->belongsTo(KelompokBidang::class, 'id_kelompok_bidang', 'id_kelompok_bidang');
    }

    public function penelitianSebelumnya(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id_penelitian_sebelumnya', 'id_kegiatan_dosen');
    }

    public function penelitianLanjutan(): HasMany
    {
        return $this->hasMany(self::class, 'id_penelitian_sebelumnya', 'id_kegiatan_dosen');
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}
