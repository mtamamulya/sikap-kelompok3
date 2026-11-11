<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DetailKegiatan extends Model
{
    protected $table = 'detail_kegiatan';

    protected $primaryKey = 'id_detail_kegiatan';

    protected $fillable = [
        'id_kegiatan', 'id_isian_kategori', 'isian',
    ];

    public function save(array $options = [])
    {
        return $this->getConnection()->transaction(fn () => parent::save($options));
    }

    protected static function booted(): void
    {
        static::saving(function (self $detail) {
            $activity = Kegiatan::whereKey($detail->id_kegiatan)->lockForUpdate()->first();
            $field = IsianKategori::whereKey($detail->id_isian_kategori)->lockForUpdate()->first();
            if (! $field || ! $activity || (int) $field->id_kategori !== (int) $activity->id_kategori) {
                throw ValidationException::withMessages(['id_isian_kategori' => 'Isian harus berasal dari kategori kegiatan yang sama.']);
            }
            if ($detail->exists && ($detail->isDirty('id_kegiatan') || $detail->isDirty('id_isian_kategori')) && $detail->dokumen()->exists()) {
                throw ValidationException::withMessages(['id_kegiatan' => 'Jawaban yang memiliki dokumen tidak dapat dipindahkan.']);
            }
            $rule = match ($field->jenis_input) {
                'angka' => 'numeric',
                'tanggal' => 'date_format:Y-m-d',
                default => 'string',
            };
            Validator::make(['isian' => $detail->isian], ['isian' => ['required', $rule]])->validate();
            $count = self::where('id_kegiatan', $activity->getKey())->where('id_isian_kategori', $field->getKey())
                ->when($detail->exists, fn ($query) => $query->whereKeyNot($detail->getKey()))->count();
            if ($field->jumlah_isian !== null && $count >= $field->jumlah_isian) {
                throw ValidationException::withMessages(['isian' => 'Jumlah jawaban melebihi batas isian kategori.']);
            }
        });
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function isianKategori(): BelongsTo
    {
        return $this->belongsTo(IsianKategori::class, 'id_isian_kategori', 'id_isian_kategori');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'id_detail_kegiatan', 'id_detail_kegiatan');
    }
}