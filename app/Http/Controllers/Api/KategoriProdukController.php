<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KategoriProdukController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => KategoriProduk::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['id'] = (string) Str::uuid();

        $kategori = KategoriProduk::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'data' => $kategori
        ], 201);
    }

    public function show(KategoriProduk $kategori)
    {
        return response()->json(['success' => true, 'data' => $kategori], 200);
    }

    public function update(Request $request, KategoriProduk $kategori)
    {
        $validator = Validator::make($request->all(), [
            'nama'      => 'sometimes|required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $kategori->update($request->all());
        return response()->json(['success' => true, 'message' => 'Kategori diperbarui.', 'data' => $kategori], 200);
    }

    public function destroy(KategoriProduk $kategori)
    {
        $kategori->delete();
        return response()->json(['success' => true, 'message' => 'Kategori dihapus.'], 200);
    }
}