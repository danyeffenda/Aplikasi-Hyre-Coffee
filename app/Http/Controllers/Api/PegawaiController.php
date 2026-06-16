<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\Peran; // <-- Tambahkan model Peran
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::with('pengguna')->get();
        return response()->json(['success' => true, 'data' => $pegawai], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:255',
            'email'         => 'required|string|email|unique:pengguna,email',
            'password'      => 'required|string|min:6',
            'kode_pegawai'  => 'required|string|unique:pegawai,kode_pegawai',
            'jabatan'       => 'required|string',
            'jenis_kelamin' => 'nullable|string',
            'alamat'        => 'nullable|string',
            'tanggal_masuk' => 'required|date',
            'gaji'          => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // CARI UUID PERAN BERDASARKAN NAMA JABATAN (Kasir/Admin/Logistik)
        // Catatan: Pastikan kolom nama peran di tabel 'peran' Anda bernama 'nama' atau sesuaikan kodenya di bawah ini.
        $peran = Peran::where('nama', $request->jabatan)->first();
        
        if (!$peran) {
            return response()->json([
                'success' => false, 
                'errors' => ['jabatan' => ["Jabatan '{$request->jabatan}' tidak ditemukan di tabel peran."]]
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 1. Insert ke tabel Pengguna (Sesuai dengan struktur baru)
            $penggunaId = (string) Str::uuid();
            $pengguna = Pengguna::create([
                'id'              => $penggunaId,
                'peran_id'        => $peran->id, // Menggunakan UUID dari tabel peran
                'nama_lengkap'    => $request->nama,
                'email'           => $request->email,
                'kata_sandi_hash' => Hash::make($request->password), // Kolom password custom
                'aktif'           => true,
            ]);

            // 2. Insert ke tabel Pegawai
            $pegawai = Pegawai::create([
                'id'            => (string) Str::uuid(),
                'pengguna_id'   => $penggunaId,
                'kode_pegawai'  => $request->kode_pegawai,
                'jabatan'       => $request->jabatan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat'        => $request->alamat,
                'tanggal_masuk' => $request->tanggal_masuk,
                'gaji'          => $request->gaji,
            ]);

            DB::commit(); 

            return response()->json([
                'success' => true,
                'message' => 'Akun Pegawai baru berhasil didaftarkan!',
                'data'    => $pegawai->load('pengguna')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akun pegawai.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::find($id);

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Pegawai tidak ditemukan.'], 404);
        }

        DB::beginTransaction();
        try {
            $penggunaId = $pegawai->pengguna_id;
            $pegawai->delete();
            Pengguna::destroy($penggunaId);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data pegawai dan akun login berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data.', 'error' => $e->getMessage()], 500);
        }
    }
}