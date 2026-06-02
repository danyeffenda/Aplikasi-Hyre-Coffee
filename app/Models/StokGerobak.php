<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StokGerobak extends Model
{
    use HasUuids;

    protected $table = 'stok_gerobak';
    public $timestamps = true;
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'gerobak_id', 'bahan_baku_id', 'jumlah_saat_ini', 'terakhir_diperbarui'
    ];
}