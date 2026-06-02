<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gerobak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GerobakController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua gerobak hyre coffee berhasil diambil.',
            'data' => Gerobak::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_gerobak'    => 'required|string|max:50',
            'nama_gerobak'    => 'required|string|max:100',
            'status'          => 'required|string',
            'lokasi_sekarang' => 'required|string',
            'catatan'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Kita buat UUID manual jika Supabase tidak otomatis meng-generate-nya
        $data = $request->all();
        $data['id'] = (string) Str::uuid();

        $gerobak = Gerobak::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Gerobak hyre coffee baru berhasil didaftarkan!',
            'data' => $gerobak
        ], 201);
    }

    public function show(Gerobak $gerobak)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data gerobak ditemukan.',
            'data' => $gerobak
        ], 200);
    }

    public function update(Request $request, Gerobak $gerobak)
    {
        $validator = Validator::make($request->all(), [
            'kode_gerobak'    => 'sometimes|required|string|max:50',
            'nama_gerobak'    => 'sometimes|required|string|max:100',
            'status'          => 'sometimes|required|string',
            'lokasi_sekarang' => 'sometimes|required|string',
            'catatan'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $gerobak->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data gerobak berhasil diperbarui.',
            'data' => $gerobak
        ], 200);
    }

    public function destroy(Gerobak $gerobak)
    {
        $gerobak->delete();
        return response()->json([
            'success' => true,
            'message' => 'Gerobak telah berhasil dihapus dari sistem hyre coffee.'
        ], 200);
    }
}