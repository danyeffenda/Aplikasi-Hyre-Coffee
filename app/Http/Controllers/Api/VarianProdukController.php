<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VarianProdukController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => VarianProduk::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id'        => 'nullable|exists:produk,id',
            'kategori_pilihan' => 'required|string|max:255', // Sekarang wajib diisi
            'nama_varian'      => 'nullable|string|max:255', // Boleh kosong sesuai skema baru
            'harga_tambahan'   => 'required|numeric',        // Wajib diisi (bisa 0)
            'sku'              => 'nullable|string|max:100',
            'aktif'            => 'boolean',
        ]);

        $data = $request->all();
        
        if (!isset($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }
        
        $data['aktif'] = $data['aktif'] ?? true;

        $varian = VarianProduk::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data varian berhasil ditambahkan.',
            'data'    => $varian
        ], 201);
    }
}