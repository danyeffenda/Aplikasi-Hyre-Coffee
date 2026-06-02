<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index()
    {
        // Menggunakan with('kategori') agar data kategori ikut muncul
        return response()->json([
            'success' => true,
            'data' => Produk::with('kategori')->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|uuid',
            'kode_produk'        => 'required|string|max:50',
            'nama'               => 'required|string|max:100',
            'harga_dasar'        => 'required|numeric',
            'aktif'              => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['id'] = (string) Str::uuid();

        $produk = Produk::create($data);

        return response()->json([
            'success' => true, 
            'message' => 'Produk berhasil ditambahkan!', 
            'data' => $produk
        ], 201);
    }

    public function show(Produk $produk)
    {
        // Memuat relasi kategori untuk produk spesifik
        return response()->json(['success' => true, 'data' => $produk->load('kategori')], 200);
    }

    public function update(Request $request, Produk $produk)
    {
        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'sometimes|required|uuid',
            'kode_produk'        => 'sometimes|required|string|max:50',
            'nama'               => 'sometimes|required|string|max:100',
            'harga_dasar'        => 'sometimes|required|numeric',
            'aktif'              => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $produk->update($request->all());
        return response()->json(['success' => true, 'message' => 'Produk diperbarui.', 'data' => $produk], 200);
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return response()->json(['success' => true, 'message' => 'Produk dihapus.'], 200);
    }
}