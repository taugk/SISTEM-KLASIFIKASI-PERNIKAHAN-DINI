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
        'id_pernikahan',
    ];

    public function pernikahan()
    {
        return $this->belongsTo(DataPernikahan::class, 'id_pernikahan');
    }
}
