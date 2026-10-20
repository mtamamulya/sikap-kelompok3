<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'id_role',
        'email',
        'password',
        'aktif_sampai',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif_sampai' => 'date',
        ];
    }

    /**
     * Akun tanpa aktif_sampai berlaku selamanya.
     * Yang terisi masih berlaku sampai akhir hari pada tanggal itu.
     */
    public function masihAktif(): bool
    {
        return $this->aktif_sampai === null
            || $this->aktif_sampai->endOfDay()->isFuture();
    }

    /**
     * Pakai User::aktif()->get() untuk mengambil akun yang masih berlaku saja.
     */
    public function scopeAktif(Builder $query): void
    {
        $query->where(function (Builder $q) {
            $q->whereNull('aktif_sampai')
                ->orWhereDate('aktif_sampai', '>=', now()->toDateString());
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function dosen(): HasOne
    {
        return $this->hasOne(Dosen::class, 'id_user', 'id_user');
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class, 'id_user', 'id_user');
    }
}
