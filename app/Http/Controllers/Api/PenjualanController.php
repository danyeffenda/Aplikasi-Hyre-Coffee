<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\VarianProduk;
use App\Models\Resep;
use App\Models\DetailResep;
use App\Models\StokGerobak;
use App\Models\MutasiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function store(Request $request)
    {
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

        DB::beginTransaction();

        try {
            $tanggal = Carbon::now();
            $nomorPenjualan = 'TRX-' . $tanggal->format('Ymd') . '-' . strtoupper(Str::random(4));

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += ($item['jumlah'] * $item['harga_satuan']);
            }

            $penjualan = Penjualan::create([
                'id'                => (string) Str::uuid(),
                'nomor_penjualan'   => $nomorPenjualan,
                'gerobak_id'        => $request->gerobak_id,
                'kasir_id'          => $request->kasir_id,
                'pelanggan_id'      => $request->pelanggan_id,
                'tanggal_penjualan' => $tanggal,
                'status_penjualan'  => 'selesai',
                'subtotal'          => $subtotal,
                'jumlah_diskon'     => 0,
                'jumlah_pajak'      => 0,
                'total_penjualan'   => $subtotal,
                'catatan'           => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $itemSubtotal = $item['jumlah'] * $item['harga_satuan'];
                
                DetailPenjualan::create([
                    'id'               => (string) Str::uuid(),
                    'penjualan_id'     => $penjualan->id,
                    'varian_produk_id' => $item['varian_produk_id'],
                    'jumlah'           => $item['jumlah'],
                    'harga_satuan'     => $item['harga_satuan'],
                    'subtotal'         => $itemSubtotal,
                ]);

                $varian = VarianProduk::find($item['varian_produk_id']);
                
                if ($varian) {
                    $resep = Resep::where('produk_id', $varian->produk_id)->first();

                    if ($resep) {
                        $detailReseps = DetailResep::where('resep_id', $resep->id)->get();

                        foreach ($detailReseps as $dR) {
                            $totalBahanDipakai = $item['jumlah'] * $dR->jumlah;

                            $stokGerobak = StokGerobak::where('gerobak_id', $request->gerobak_id)
                                                      ->where('bahan_baku_id', $dR->bahan_baku_id)
                                                      ->first();

                            // =========================================================
                            // VALIDASI: CEK KETERSEDIAAN STOK SEBELUM DIPOTONG
                            // =========================================================
                            $sisaStok = $stokGerobak ? $stokGerobak->jumlah_saat_ini : 0;
                            
                            if (!$stokGerobak || $sisaStok < $totalBahanDipakai) {
                                // Ambil nama bahan baku agar pesan error mudah dibaca kasir
                                $namaBahan = DB::table('bahan_baku')->where('id', $dR->bahan_baku_id)->value('nama') ?? 'Bahan Tidak Diketahui';
                                
                                // Gagalkan proses dengan melempar Exception
                                throw new \Exception("Stok {$namaBahan} tidak mencukupi! Sisa: {$sisaStok}, Dibutuhkan: {$totalBahanDipakai}");
                            }
                            // =========================================================

                            // Jika lolos validasi, kurangi stok seperti biasa
                            $stokGerobak->jumlah_saat_ini -= $totalBahanDipakai;
                            $stokGerobak->terakhir_diperbarui = Carbon::now();
                            $stokGerobak->save();

                            MutasiStok::create([
                                'id'              => (string) Str::uuid(),
                                'stok_gerobak_id' => $stokGerobak->id,
                                'jenis_mutasi'    => 'keluar',
                                'tipe_referensi'  => 'penjualan',
                                'id_referensi'    => $penjualan->id,
                                'jumlah'          => $totalBahanDipakai,
                                'catatan'         => 'Pengurangan otomatis sistem POS',
                                'tanggal_mutasi'  => Carbon::now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $penjualan->load('detail');

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat dan stok gerobak telah diperbarui!',
                'data'    => $penjualan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Menangkap pesan error dari validasi stok dan menampilkannya
            return response()->json([
                'success' => false,
                'message' => 'Transaksi Gagal!',
                'error'   => $e->getMessage()
            ], 400); // Mengubah status menjadi 400 Bad Request
        }
    }
}