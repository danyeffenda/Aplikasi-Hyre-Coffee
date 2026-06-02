<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VarianProduk extends Model
{
    use HasUuids;

    protected $table = 'varian_produk';
    public $timestamps = true;
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'produk_id', 'nama_varian', 'label_ukuran', 
        'suhu', 'harga_tambahan', 'sku', 'aktif'
    ];
}