<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Gerobak extends Model
{
    use HasUuids;

    // 1. Arahkan ke nama tabel kustom di Supabase
    protected $table = 'gerobak';

    // 2. Gunakan timestamps otomatis tapi beri tahu Laravel nama kolom kustom Anda
    public $timestamps = true; // Set true agar Laravel mengisi otomatis
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    // 3. Set agar primary key membaca UUID (String)
    protected $keyType = 'string';
    public $incrementing = false;

    // 4. Daftarkan kolom yang boleh diisi massal
    protected $fillable = [
        'id',             // Penting untuk diisi jika Anda meng-generate UUID di sisi aplikasi
        'kode_gerobak',
        'nama_gerobak',
        'status',
        'lokasi_sekarang',
        'catatan',
    ];
}