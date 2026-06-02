<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Penjualan extends Model
{
    use HasUuids;

    protected $table = 'penjualan';
    public $timestamps = true;
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'nomor_penjualan', 'gerobak_id', 'pelanggan_id', 
        'kasir_id', 'tanggal_penjualan', 'status_penjualan', 
        'subtotal', 'jumlah_diskon', 'jumlah_pajak', 'total_penjualan', 'catatan'
    ];

    // Relasi ke Detail Penjualan (Satu penjualan punya banyak detail/produk)
    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id', 'id');
    }
}