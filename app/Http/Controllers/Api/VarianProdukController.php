<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VarianProdukController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'produk_id'      => 'required|uuid',
            'nama_varian'    => 'required|string',
            'label_ukuran'   => 'nullable|string',
            'suhu'           => 'nullable|string',
            'harga_tambahan' => 'nullable|numeric',
        ]);

        $data = $request->all();
        $data['id'] = (string) Str::uuid();
        $data['harga_tambahan'] = $data['harga_tambahan'] ?? 0;

        $varian = VarianProduk::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Varian berhasil ditambahkan',
            'data'    => $varian
        ], 201);
    }
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => \App\Models\VarianProduk::all()
        ], 200);
    }
}