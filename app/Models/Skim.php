<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skim extends Model
{
    protected $table = 'skim';

    protected $primaryKey = 'id_skim';

    protected $fillable = [
        'nama_skim',
        'ranah',
    ];
}
