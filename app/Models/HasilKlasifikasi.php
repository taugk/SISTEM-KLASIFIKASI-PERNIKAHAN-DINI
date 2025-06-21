<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilKlasifikasi extends Model
{
    use HasFactory;

    protected $table = 'hasil_klasifikasi';

    protected $fillable = [
        'kategori_pernikahan',
        'probabilitas',
        'akurasi',
        'confidence',
        'akurasi',
        'id_pernikahan',
        'penyebab',
        'dampak',
    ];

    public function pernikahan()
    {
        return $this->belongsTo(DataPernikahan::class, 'id_pernikahan');
    }
}
