<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataEdukasi extends Model
{
    use HasFactory;
    protected $table = 'data_edukasi';
    protected $primaryKey = 'kd_edukasi';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kd_edukasi',
        'judul',
        'deskripsi',
        'gambar',
        'pengguna_id',
        'kategori',
    ];

    public $timestamps = true;

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
