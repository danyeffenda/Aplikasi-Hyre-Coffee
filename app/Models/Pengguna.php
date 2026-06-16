<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pengguna extends Authenticatable implements JWTSubject 
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    
    protected $keyType = 'string';
    public $incrementing = false;

    // Beritahu Laravel nama kolom Timestamp custom Anda
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $guarded = [];

    protected $hidden = [
        'kata_sandi_hash', // Sembunyikan kolom password custom ini saat dipanggil
        'remember_token',
    ];

    // =========================================================
    // FUNGSI OVERRIDE WAJIB UNTUK LOGIN & JWT
    // =========================================================
    
    /**
     * Beritahu Laravel bahwa kolom password kita bernama 'kata_sandi_hash'
     */
    public function getAuthPassword()
    {
        return $this->kata_sandi_hash;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // =========================================================
    // RELASI DATABASE
    // =========================================================
    public function peran()
    {
        return $this->belongsTo(\App\Models\Peran::class, 'peran_id'); 
    }

    public function pegawai()
    {
        return $this->hasOne(\App\Models\Pegawai::class, 'pengguna_id', 'id');
    }
}