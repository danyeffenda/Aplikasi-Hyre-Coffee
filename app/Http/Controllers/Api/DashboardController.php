<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
        public function ringkasan()
    {
        // Menggunakan timezone Asia/Jakarta untuk memastikan filter 'hari ini' akurat
        $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();

        // 1. Hitung Master Data
        $totalProduk = \App\Models\Produk::count();
        $totalPegawai = \App\Models\Pegawai::count();

        // 2. Hitung Data Penjualan
        // Menggunakan kolom 'dibuat_pada' sesuai model Penjualan.php
        // Menggunakan kolom 'total_penjualan' sesuai model Penjualan.php
        $transaksiHariIni = \Illuminate\Support\Facades\DB::table('penjualan')
            ->whereDate('dibuat_pada', $hariIni)
            ->count();

        $pendapatanHariIni = \Illuminate\Support\Facades\DB::table('penjualan')
            ->whereDate('dibuat_pada', $hariIni)
            ->sum('total_penjualan'); 

        return response()->json([
            'success' => true,
            'data' => [
                'total_produk'        => $totalProduk,
                'total_pegawai'       => $totalPegawai,
                'transaksi_hari_ini'  => $transaksiHariIni,
                'pendapatan_hari_ini' => $pendapatanHariIni,
            ]
        ], 200);
    }
}