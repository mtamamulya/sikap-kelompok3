<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class IsianKategori extends Model
{
    protected $table = 'isian_kategori';

    protected $primaryKey = 'id_isian_kategori';

    protected $fillable = ['id_kategori', 'input', 'jenis_input', 'aturan', 'jumlah_isian'];

    protected $attributes = ['jenis_input' => 'teks', 'aturan' => 'NULL', 'jumlah_isian' => 1];

    protected function casts(): array
    {
        return ['jumlah_isian' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $field) {
            if ($field->jumlah_isian !== null && $field->jumlah_isian < 1) {
                throw ValidationException::withMessages(['jumlah_isian' => 'Batas jawaban minimal satu, atau NULL untuk tanpa batas.']);
            }
            if ($field->exists && ($field->isDirty('id_kategori') || $field->isDirty('jenis_input')) && $field->detailKegiatan()->exists()) {
                throw ValidationException::withMessages(['id_kategori' => 'Kategori dan jenis isian yang sudah dijawab tidak dapat diubah.']);
            }
        });
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function detailKegiatan(): HasMany
    {
        return $this->hasMany(DetailKegiatan::class, 'id_isian_kategori', 'id_isian_kategori');
    }
}
