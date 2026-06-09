<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Peran extends Model
{
    use HasUuids; // Karena Primary Key-nya menggunakan UUID

    // Arahkan ke nama tabel yang benar di database
    protected $table = 'peran';

    // Matikan timestamps bawaan jika tidak ada created_at/updated_at standar
    public $timestamps = false;

    // Konfigurasi Primary Key UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // Kolom yang boleh diisi
    protected $fillable = [
        'nama',
        'deskripsi'
    ];
}