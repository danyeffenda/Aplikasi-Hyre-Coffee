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
        $hariIni = Carbon::today()->toDateString();

        // 1. Hitung Master Data
        $totalProduk = Produk::count();
        $totalPegawai = Pegawai::count();

        // 2. Hitung Data Penjualan (Kita gunakan try-catch agar aman jika struktur tabel penjualan Anda berbeda)
        $transaksiHariIni = 0;
        $pendapatanHariIni = 0;

        try {
            // Asumsi: tabel bernama 'penjualan' dan memiliki kolom 'dibuat_pada' serta 'total_harga'
            // Silakan sesuaikan 'total_harga' dengan nama kolom asli di tabel Anda jika berbeda
            $transaksiHariIni = DB::table('penjualan')
                ->whereDate('dibuat_pada', $hariIni)
                ->count();

            // Uncomment baris di bawah ini jika Anda sudah memiliki kolom total pemasukan di tabel penjualan
            /*
            $pendapatanHariIni = DB::table('penjualan')
                ->whereDate('dibuat_pada', $hariIni)
                ->sum('total_harga'); 
            */
        } catch (\Exception $e) {
            // Abaikan error jika tabel penjualan belum siap, kirim 0
        }

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