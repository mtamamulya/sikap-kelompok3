<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AnggotaKegiatan extends Model
{
    public const PERAN = ['ketua', 'anggota'];

    protected $table = 'anggota_kegiatan';

    protected $primaryKey = 'id_anggota_kegiatan';

    protected $fillable = ['id_kegiatan', 'id_prestasi', 'id_user', 'peran'];

    protected static function booted(): void
    {
        static::saving(function (self $anggota) {
            if (($anggota->id_kegiatan === null) === ($anggota->id_prestasi === null)) {
                throw ValidationException::withMessages([
                    'id_kegiatan' => 'Anggota harus milik tepat satu kegiatan atau satu prestasi.',
                ]);
            }
            if (! in_array($anggota->peran, self::PERAN, true)) {
                throw ValidationException::withMessages(['peran' => 'Peran hanya boleh ketua atau anggota.']);
            }
            if ($anggota->id_prestasi
                && Prestasi::whereKey($anggota->id_prestasi)->whereNotNull('id_kegiatan')->exists()) {
                throw ValidationException::withMessages([
                    'id_prestasi' => 'Prestasi yang terkait kegiatan memakai anggota kegiatan tersebut.',
                ]);
            }
        });

        static::deleting(function (self $anggota) {
            if ((int) $anggota->id_user === (int) $anggota->idPemilik()) {
                throw ValidationException::withMessages(['id_user' => 'Pengaju tidak dapat dikeluarkan dari tim.']);
            }
        });
    }

    public function idPemilik(): ?int
    {
        return $this->id_kegiatan
            ? Kegiatan::whereKey($this->id_kegiatan)->value('id_user')
            : Prestasi::whereKey($this->id_prestasi)->value('id_user');
    }

    /**
     * Simpan seluruh daftar tim sekaligus.
     * $daftar: [['id_user' => 1, 'peran' => 'ketua'], ['id_user' => 2, 'peran' => 'anggota'], ...]
     */
    public static function simpanDaftar(Kegiatan|Prestasi $induk, array $daftar): void
    {
        $kolom = $induk instanceof Kegiatan ? 'id_kegiatan' : 'id_prestasi';
        $pemilik = (int) $induk->id_user;
        $daftar = collect($daftar)->unique('id_user')->values();

        if ($daftar->count() < 2) {
            throw ValidationException::withMessages(['anggota' => 'Tim minimal terdiri dari dua orang.']);
        }
        if (! $daftar->contains(fn ($a) => (int) $a['id_user'] === $pemilik)) {
            throw ValidationException::withMessages(['anggota' => 'Pengaju harus tetap menjadi anggota tim.']);
        }
        if ($daftar->where('peran', 'ketua')->count() !== 1) {
            throw ValidationException::withMessages(['anggota' => 'Tim harus memiliki tepat satu ketua.']);
        }

        DB::transaction(function () use ($kolom, $induk, $daftar) {
            static::where($kolom, $induk->getKey())
                ->whereNotIn('id_user', $daftar->pluck('id_user'))
                ->get()
                ->each->delete();

            foreach ($daftar as $a) {
                static::updateOrCreate(
                    [$kolom => $induk->getKey(), 'id_user' => $a['id_user']],
                    ['peran' => $a['peran']],
                );
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function prestasi(): BelongsTo
    {
        return $this->belongsTo(Prestasi::class, 'id_prestasi', 'id_prestasi');
    }
}