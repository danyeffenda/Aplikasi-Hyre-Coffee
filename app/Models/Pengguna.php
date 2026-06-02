<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pengguna extends Authenticatable implements JWTSubject
{
    use Notifiable, HasUuids;

    // 1. Memastikan model ini mengarah ke tabel kustom Anda, bukan 'users'
    protected $table = 'pengguna'; 
    
    // REVISI: Matikan timestamps otomatis karena tabel Anda tidak memiliki kolom 'created_at' dan 'updated_at'
    public $timestamps = false;
    
    // 2. Konfigurasi agar Laravel tahu Primary Key Anda berformat UUID (String)
    protected $keyType = 'string';
    public $incrementing = false;

    // 3. Daftarkan kolom yang boleh diisi (Mass Assignable)
    protected $fillable = [
        'peran_id',
        'nama_lengkap',
        'email',
        'no_hp',
        'kata_sandi_hash',
        'aktif',
    ];

    // 4. Sembunyikan kata sandi saat data pengguna di-panggil dalam bentuk JSON/API
    protected $hidden = [
        'kata_sandi_hash',
    ];

    /**
     * Method Wajib dari JWTSubject:
     * Mengambil ID unik dari pengguna untuk dimasukkan ke dalam klaim Token.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Method Wajib dari JWTSubject:
     * Menyisipkan informasi tambahan ke dalam Token. Kita masukkan 'peran_id'
     * agar frontend tahu hak akses akun ini tanpa harus hit API berkali-kali.
     */
    public function getJWTCustomClaims()
    {
        return [
            'peran_id' => $this->peran_id
        ];
    }

    /**
     * Override Password Bawaan Laravel:
     * Laravel secara default mencari kolom bernama 'password'. Karena di database
     * Anda namanya 'kata_sandi_hash', kita harus belokkan lewat fungsi ini.
     */
    public function getAuthPassword()
    {
        return $this->kata_sandi_hash;
    }
}