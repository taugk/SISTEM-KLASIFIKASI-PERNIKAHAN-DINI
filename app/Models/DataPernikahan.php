<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPernikahan extends Model
{
    use HasFactory;

    protected $table = "pernikahan";

    protected $fillable = [
        'nama_suami',
        'tanggal_lahir_suami',
        'usia_suami',
        'pendidikan_suami',
        'pekerjaan_suami',
        'status_suami',

        // Istri
        'nama_istri',
        'tanggal_lahir_istri',
        'usia_istri',
        'pendidikan_istri',
        'status_istri',
        'pekerjaan_istri',

        // Data pernikahan
        'tanggal_akad',
        'wilayah_id',

    ];

    protected $casts = [
        'tanggal_lahir_suami' => 'date',
        'tanggal_lahir_istri' => 'date',
        'tanggal_akad' => 'date',
    ];

    public function wilayah(){
        return $this->belongsTo(DataWilayah::class, 'wilayah_id', 'id');
    }

    public function hasilKlasifikasi(){
        return $this->hasOne(HasilKlasifikasi::class, 'id_pernikahan', 'id');
    }
}
