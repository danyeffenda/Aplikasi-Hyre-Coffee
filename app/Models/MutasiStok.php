<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MutasiStok extends Model
{
    use HasUuids;

    protected $table = 'mutasi_stok';
    public $timestamps = true;
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'stok_gerobak_id', 'jenis_mutasi', 'tipe_referensi', 
        'id_referensi', 'jumlah', 'catatan', 'tanggal_mutasi'
    ];
}