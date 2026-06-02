<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GerobakController;

// Rute Login (Bisa diakses tanpa token)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Grup Rute yang Wajib membawa JWT Token
Route::middleware('auth:api')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Rute Gerobak
    Route::apiResource('gerobak', GerobakController::class);
    Route::apiResource('kategori', \App\Http\Controllers\Api\KategoriProdukController::class);
    Route::apiResource('produk', \App\Http\Controllers\Api\ProdukController::class);
    Route::apiResource('penjualan', \App\Http\Controllers\Api\PenjualanController::class);
    Route::apiResource('varian-produk', \App\Http\Controllers\Api\VarianProdukController::class);
});