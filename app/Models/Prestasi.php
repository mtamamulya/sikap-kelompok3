<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class Prestasi extends Model
{
    protected $table = 'prestasi';

    protected $primaryKey = 'id_prestasi';

    protected $fillable = [
        'id_user', 'created_by', 'id_kegiatan', 'capaian', 'jenis_prestasi',
        'bidang_lomba', 'nama_lomba', 'penyelenggara', 'lokasi', 'dosen_pembimbing',
        'tingkat_kejuaraan', 'tanggal_mulai', 'tanggal_selesai',
        'bentuk_partisipasi', 'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $prestasi) {
            if ($prestasi->tanggal_mulai && $prestasi->tanggal_selesai
                && $prestasi->tanggal_selesai->lt($prestasi->tanggal_mulai)) {
                throw ValidationException::withMessages(['tanggal_selesai' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.']);
            }

            if ($prestasi->id_kegiatan) {
                $user = User::find($prestasi->id_user);
                if (! Kegiatan::whereKey($prestasi->id_kegiatan)->untukPrestasi($user)->exists()) {
                    throw ValidationException::withMessages(['id_kegiatan' => 'Kegiatan harus sudah diterima dan melibatkan pemilik prestasi.']);
                }
            }
        });
    }

    public function isTim(): bool
    {
        return $this->bentuk_partisipasi === 'tim';
    }

    /** Semua orang yang menerima prestasi ini, tanpa duplikat. */
    public function daftarPenerima(): Collection
    {
        if ($this->id_kegiatan) {
            $kegiatan = $this->kegiatan()->with('user', 'anggota.user')->first();

            return $kegiatan->anggota->pluck('user')
                ->push($kegiatan->user)
                ->unique('id_user')
                ->values();
        }

        return $this->anggota()->with('user')->get()->pluck('user')
            ->push($this->user)
            ->unique('id_user')
            ->values();
    }

    public function scopeUntukUser(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(fn (Builder $q) => $q
            ->where('id_user', $user->getKey())
            ->orWhereHas('anggota', fn (Builder $a) => $a->where('id_user', $user->getKey()))
            ->orWhereHas('kegiatan.anggota', fn (Builder $a) => $a->where('id_user', $user->getKey())));
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

    public function anggota(): HasMany
    {
        return $this->hasMany(AnggotaKegiatan::class, 'id_prestasi', 'id_prestasi');
    }

    public function buktiPrestasi(): HasMany
    {
        return $this->hasMany(BuktiPrestasi::class, 'id_prestasi', 'id_prestasi');
    }
}