<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'id_kategori', 'id_user', 'id_kegiatan_sebelumnya',
        'nama_kegiatan', 'lokasi', 'deskripsi', 'nomor_sk',
        'tanggal_sk', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $activity) {
            if ($activity->isDirty('id_kategori') && $activity->detail()->exists()) {
                throw ValidationException::withMessages(['id_kategori' => 'Kategori kegiatan yang sudah memiliki jawaban tidak dapat langsung diubah.']);
            }
        });
    }

    public function statusTerkini(): HasOne
    {
        return $this->hasOne(Timeline::class, 'id_kegiatan', 'id_kegiatan')
            ->latestOfMany('tanggal_status');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(Timeline::class, 'id_kegiatan', 'id_kegiatan')
            ->orderBy('tanggal_status')->orderBy('id_timeline');
    }

    public function scopeUntukUser(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(fn (Builder $q) => $q->where('id_user', $user->getKey())
            ->orWhereHas('anggota', fn (Builder $q) => $q->where('id_user', $user->getKey())));
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailKegiatan::class, 'id_kegiatan', 'id_kegiatan')->orderBy('id_detail_kegiatan');
    }

    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function nilaiIsian(string $input): ?string
    {
        $details = $this->relationLoaded('detail') ? $this->detail : $this->detail()->with('isianKategori')->get();

        return $details->first(fn ($detail) => $detail->isianKategori?->input === $input)?->isian;
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'id_kegiatan')->orderBy('id_dokumen');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(AnggotaKegiatan::class, 'id_kegiatan');
    }
}
