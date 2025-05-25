<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $jumlah_pernikahan
 * @property int $jumlah_pernikahan_dini
 * @property string $periode
 * @property string $resiko_wilayah
 * @property int $id_wilayah
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataWilayah $wilayah
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah whereIdWilayah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah whereJumlahPernikahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah whereJumlahPernikahanDini($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah wherePeriode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah whereResikoWilayah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resiko_Wilayah whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
