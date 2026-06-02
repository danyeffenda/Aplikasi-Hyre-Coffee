<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class AuthController extends Controller
{
    /**
     * LOGIC LOGIN SATU PINTU (Admin, Logistik, Kasir)
     */
    public function login(Request $request)
    {
        // 1. Validasi Input Format Email dan Password
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Cari Pengguna Berdasarkan Email
        $user = Pengguna::where('email', $request->email)->first();

        // 3. Verifikasi Keberadaan User dan Validitas Password
        if (!$user || !Hash::check($request->password, $user->kata_sandi_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi Anda salah.'
            ], 401);
        }

        // 4. Periksa Apakah Akun Pegawai Masih Aktif
        if (!$user->aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi Admin Pusat.'
            ], 403);
        }

        // 5. Generate JWT Token menggunakan Guard 'api'
        if (!$token = auth('api')->login($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token autentikasi.'
            ], 500);
        }

        // 6. Response Sukses - Kirim Token dan Detail Peran ke Frontend
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => [
                    'id' => $user->id,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email,
                    'peran_id' => $user->peran_id
                ]
            ]
        ], 200);
    }

    /**
     * AMBIL PROFIL USER YANG SEDANG LOGIN (Berdasarkan Token)
     */
    public function me()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil.',
            'data' => auth('api')->user()
        ], 200);
    }

    /**
     * LOGOUT (Membatalkan/Membakar Token Aktif)
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dari sistem (Logout).'
        ], 200);
    }
}