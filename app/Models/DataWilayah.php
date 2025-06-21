<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataWilayah extends Model
{
    use HasFactory;
    protected $table = 'data_wilayah';

    protected $fillable = [
        'id',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'desa',
    ];



    public $timestamps = true;

    public function wilayah(){
        return $this->hasMany(DataPernikahan::class, 'wilayah_id', 'id');
    }

    public function pernikahan(){
        return $this->hasMany(DataPernikahan::class, 'wilayah_id', 'id');
    }

    public function resiko_wilayah(){
        return $this->hasMany(Resiko_Wilayah::class, 'id_wilayah', 'id');
    }

    public function resiko_wilayah_terbaru()
{
    return $this->hasOne(Resiko_Wilayah::class, 'id_wilayah', 'id')->latestOfMany('periode');
}



}
