<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    // 1. Beritahu nama tabel yang benar
    protected $table = 'pegawai';

    // 2. Beritahu bahwa Primary Key kita pakai UUID (String), bukan Angka (Integer)
    protected $keyType = 'string';
    public $incrementing = false;

    // 3. Beritahu Laravel nama kolom waktu (Timestamp) custom Anda
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    // Izinkan semua kolom diisi secara massal
    protected $guarded = [];

    // 4. JEMBATAN RELASI: Pegawai ini milik siapa di tabel Pengguna?
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'id');
    }
}