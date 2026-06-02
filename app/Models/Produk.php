<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Produk extends Model
{
    use HasUuids;

    protected $table = 'produk';
    public $timestamps = true;
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'kategori_produk_id', 'kode_produk', 'nama', 
        'deskripsi', 'harga_dasar', 'aktif', 'url_gambar'
    ];

    /**
     * Relasi ke KategoriProduk
     * Menggunakan 'kategori_produk_id' sebagai foreign key
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id', 'id');
    }
}