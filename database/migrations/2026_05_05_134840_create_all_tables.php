<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kategori Produk
        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 2. Peran
        Schema::create('peran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 3. Gerobak
        Schema::create('gerobak', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_gerobak')->unique();
            $table->string('nama_gerobak');
            $table->string('status')->default('aktif');
            $table->string('lokasi_sekarang')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 4. Pelanggan
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_lengkap');
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->integer('poin_loyalitas')->default(0);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 5. Pemasok
        Schema::create('pemasok', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_pemasok')->unique();
            $table->string('nama_pemasok');
            $table->string('nama_kontak')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 6. Bahan Baku
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_bahan')->unique();
            $table->string('nama');
            $table->string('satuan');
            $table->decimal('stok_minimum', 15, 2)->default(0);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 7. Promo
        Schema::create('promo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_promo')->unique();
            $table->string('nama_promo');
            $table->string('jenis_diskon');
            $table->decimal('nilai_diskon', 15, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('minimal_pembelian', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 8. Users (Bawaan Laravel)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // 9. Password Reset Tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 10. Sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
        });

        // 11. Cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value');
            $table->bigInteger('expiration');
        });

        // 12. Cache Locks
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration');
        });

        // 13. Pengguna (Relasi ke Peran)
        Schema::create('pengguna', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('peran_id')->constrained('peran')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_hp')->nullable();
            $table->text('kata_sandi_hash');
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 14. Pegawai (Relasi ke Pengguna)
        Schema::create('pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pengguna_id')->unique()->constrained('pengguna')->onDelete('cascade');
            $table->string('kode_pegawai')->unique();
            $table->string('jabatan');
            $table->string('jenis_kelamin')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_masuk');
            $table->decimal('gaji', 15, 2)->default(0);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 15. Produk (Relasi ke Kategori Produk)
        Schema::create('produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kategori_produk_id')->constrained('kategori_produk')->onDelete('cascade');
            $table->string('kode_produk')->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_dasar', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->text('url_gambar')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 16. Varian Produk (Relasi ke Produk)
        Schema::create('varian_produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('produk_id')->constrained('produk')->onDelete('cascade');
            $table->string('nama_varian');
            $table->string('label_ukuran')->nullable();
            $table->string('suhu')->nullable();
            $table->decimal('harga_tambahan', 15, 2)->default(0);
            $table->string('sku')->unique()->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 17. Resep (Relasi ke Produk)
        Schema::create('resep', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('produk_id')->unique()->constrained('produk')->onDelete('cascade');
            $table->string('nama_resep');
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 18. Detail Resep (Relasi ke Resep & Bahan Baku)
        Schema::create('detail_resep', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('resep_id')->constrained('resep')->onDelete('cascade');
            $table->foreignUuid('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 19. Pembelian (Relasi ke Pemasok & Gerobak)
        Schema::create('pembelian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pemasok_id')->constrained('pemasok')->onDelete('cascade');
            $table->foreignUuid('gerobak_id')->constrained('gerobak')->onDelete('cascade');
            $table->string('nomor_pembelian')->unique();
            $table->date('tanggal_pembelian');
            $table->string('status_pembelian')->default('draft');
            $table->decimal('total_pembelian', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 20. Detail Pembelian (Relasi ke Pembelian & Bahan Baku)
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pembelian_id')->constrained('pembelian')->onDelete('cascade');
            $table->foreignUuid('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 21. Penjualan (Relasi ke Gerobak, Pelanggan, & Pengguna sbg Kasir)
        Schema::create('penjualan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_penjualan')->unique();
            $table->foreignUuid('gerobak_id')->constrained('gerobak')->onDelete('cascade');
            $table->foreignUuid('pelanggan_id')->nullable()->constrained('pelanggan')->onDelete('set null');
            $table->foreignUuid('kasir_id')->constrained('pengguna')->onDelete('cascade');
            $table->timestamp('tanggal_penjualan')->useCurrent();
            $table->string('status_penjualan')->default('selesai');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('jumlah_diskon', 15, 2)->default(0);
            $table->decimal('jumlah_pajak', 15, 2)->default(0);
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 22. Pembayaran (Relasi ke Penjualan)
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penjualan_id')->constrained('penjualan')->onDelete('cascade');
            $table->string('metode_pembayaran');
            $table->decimal('jumlah_dibayar', 15, 2);
            $table->string('status_pembayaran')->default('berhasil');
            $table->timestamp('tanggal_pembayaran')->useCurrent();
            $table->string('nomor_referensi')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 23. Detail Penjualan (Relasi ke Penjualan & Varian Produk)
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penjualan_id')->constrained('penjualan')->onDelete('cascade');
            $table->foreignUuid('varian_produk_id')->constrained('varian_produk')->onDelete('cascade');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 24. Promo Penjualan (Relasi ke Penjualan & Promo)
        Schema::create('promo_penjualan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penjualan_id')->constrained('penjualan')->onDelete('cascade');
            $table->foreignUuid('promo_id')->constrained('promo')->onDelete('cascade');
            $table->decimal('jumlah_diskon', 15, 2);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

            // 25. Stok Gerobak (Relasi ke Gerobak & Bahan Baku)
        Schema::create('stok_gerobak', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('gerobak_id')->constrained('gerobak')->onDelete('cascade');
            $table->foreignUuid('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->decimal('jumlah_saat_ini', 15, 2)->default(0); // Optional alias for consistency
            $table->timestamp('terakhir_diperbarui')->nullable()->useCurrent();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 26. Mutasi Stok (Relasi ke Stok Gerobak)
        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stok_gerobak_id')->constrained('stok_gerobak')->onDelete('cascade');
            $table->string('jenis_mutasi');
            $table->string('tipe_referensi')->nullable();
            $table->uuid('id_referensi')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_mutasi')->useCurrent();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        // 27. Jadwal Gerobak (Relasi ke Gerobak)
        Schema::create('jadwal_gerobak', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('gerobak_id')->constrained('gerobak')->onDelete('cascade');
            $table->date('tanggal_jadwal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('nama_lokasi');
            $table->text('alamat')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Penghapusan tabel harus berlawanan arah dengan urutan pembuatannya 
        // untuk mencegah konflik foreign key
        Schema::dropIfExists('jadwal_gerobak');
        Schema::dropIfExists('mutasi_stok');
        Schema::dropIfExists('stok_gerobak');
        Schema::dropIfExists('promo_penjualan');
        Schema::dropIfExists('detail_penjualan');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('penjualan');
        Schema::dropIfExists('detail_pembelian');
        Schema::dropIfExists('pembelian');
        Schema::dropIfExists('detail_resep');
        Schema::dropIfExists('resep');
        Schema::dropIfExists('varian_produk');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('pengguna');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('promo');
        Schema::dropIfExists('bahan_baku');
        Schema::dropIfExists('pemasok');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('gerobak');
        Schema::dropIfExists('peran');
        Schema::dropIfExists('kategori_produk');
    }
};