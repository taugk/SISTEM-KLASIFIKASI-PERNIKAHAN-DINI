<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resiko_Wilayah extends Model
{
    use HasFactory;

    protected $table = 'resiko_wilayah';

    protected $fillable = [
        'jumlah_pernikahan',
        'jumlah_pernikahan_dini',
        'resiko_wilayah',
        'periode',
        'id_wilayah'
    ];

    public function wilayah()
    {
        return $this->belongsTo(DataWilayah::class, 'id_wilayah');
    }

}
