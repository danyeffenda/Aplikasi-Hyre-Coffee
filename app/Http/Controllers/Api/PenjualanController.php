<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input (Pastikan ada array 'items' yang dikirim dari kasir)
        $validator = Validator::make($request->all(), [
            'gerobak_id'               => 'required|uuid',
            'kasir_id'                 => 'required|uuid',
            'pelanggan_id'             => 'nullable|uuid',
            'catatan'                  => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.varian_produk_id' => 'required|uuid',
            'items.*.jumlah'           => 'required|integer|min:1',
            'items.*.harga_satuan'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            // Generate Nomor Penjualan Unik (Contoh: TRX-20260601-XXXX)
            $tanggal = Carbon::now();
            $nomorPenjualan = 'TRX-' . $tanggal->format('Ymd') . '-' . strtoupper(Str::random(4));

            // Hitung Subtotal dari seluruh item di keranjang
            $subtotal = 0;
            $items = $request->items;
            foreach ($items as $item) {
                $subtotal += ($item['jumlah'] * $item['harga_satuan']);
            }

            // Simpan Header Penjualan ke tabel `penjualan`
            $penjualan = Penjualan::create([
                'id'                => (string) Str::uuid(),
                'nomor_penjualan'   => $nomorPenjualan,
                'gerobak_id'        => $request->gerobak_id,
                'kasir_id'          => $request->kasir_id,
                'pelanggan_id'      => $request->pelanggan_id,
                'tanggal_penjualan' => $tanggal,
                'status_penjualan'  => 'selesai',
                'subtotal'          => $subtotal,
                'jumlah_diskon'     => 0, // Bisa dikembangkan nanti dengan tabel promo
                'jumlah_pajak'      => 0,
                'total_penjualan'   => $subtotal,
                'catatan'           => $request->catatan,
            ]);

            // Simpan Detail Keranjang ke tabel `detail_penjualan`
            foreach ($items as $item) {
                $itemSubtotal = $item['jumlah'] * $item['harga_satuan'];
                
                DetailPenjualan::create([
                    'id'               => (string) Str::uuid(),
                    'penjualan_id'     => $penjualan->id,
                    'varian_produk_id' => $item['varian_produk_id'],
                    'jumlah'           => $item['jumlah'],
                    'harga_satuan'     => $item['harga_satuan'],
                    'subtotal'         => $itemSubtotal,
                ]);
            }

            // Jika semua lancar, Commit (Simpan permanen ke database)
            DB::commit();

            // Load data detail agar respon API menampilkan struk yang lengkap
            $penjualan->load('detail');

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat!',
                'data'    => $penjualan
            ], 201);

        } catch (\Exception $e) {
            // Jika ada error (misal koneksi putus), batalkan semua input
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencatat transaksi.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}