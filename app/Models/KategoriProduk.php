<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KategoriProduk extends Model
{
    use HasUuids;

    protected $table = 'kategori_produk';
    public $timestamps = true;
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';
    protected $fillable = ['id', 'nama', 'deskripsi'];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_produk_id', 'id');
    }
} 