<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // <-- Wajib untuk mengelola file

class ProdukController extends Controller
{
    public function index()
    {
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
            // Validasi file gambar maksimal 2MB
            'url_gambar'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['id'] = (string) Str::uuid();

        // LOGIKA UPLOAD GAMBAR BARU
        if ($request->hasFile('url_gambar')) {
            // Simpan ke folder 'storage/app/public/produk'
            $path = $request->file('url_gambar')->store('produk', 'public');
            $data['url_gambar'] = $path; // Simpan path (misal: produk/file.jpg) ke database
        }

        $produk = Produk::create($data);

        return response()->json([
            'success' => true, 
            'message' => 'Produk berhasil ditambahkan!', 
            'data' => $produk
        ], 201);
    }

    public function show(Produk $produk)
    {
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
            'url_gambar'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // LOGIKA UPDATE GAMBAR
        if ($request->hasFile('url_gambar')) {
            // Hapus gambar lama dari server jika ada
            if ($produk->url_gambar && Storage::disk('public')->exists($produk->url_gambar)) {
                Storage::disk('public')->delete($produk->url_gambar);
            }
            // Simpan gambar baru
            $path = $request->file('url_gambar')->store('produk', 'public');
            $data['url_gambar'] = $path;
        }

        $produk->update($data);
        return response()->json(['success' => true, 'message' => 'Produk diperbarui.', 'data' => $produk], 200);
    }

    public function destroy(Produk $produk)
    {
        // Hapus file fisik gambar saat produk dihapus dari database
        if ($produk->url_gambar && Storage::disk('public')->exists($produk->url_gambar)) {
            Storage::disk('public')->delete($produk->url_gambar);
        }
        
        $produk->delete();
        return response()->json(['success' => true, 'message' => 'Produk dihapus.'], 200);
    }
}