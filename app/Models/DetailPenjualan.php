<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DetailPenjualan extends Model
{
    use HasUuids;

    protected $table = 'detail_penjualan';
    public $timestamps = true;
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'penjualan_id', 'varian_produk_id', 'jumlah', 
        'harga_satuan', 'subtotal', 'catatan'
    ];

    // Relasi balik ke Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id', 'id');
    }
}