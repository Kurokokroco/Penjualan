<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->index('nama_pelanggan');
        });
        Schema::table('produk', function (Blueprint $table) {
            $table->index('nama_produk');
            $table->index('stok');
        });
        Schema::table('penjualan', function (Blueprint $table) {
            $table->index('tanggal_penjualan');
            $table->index('id_pelanggan');
        });
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->index('id_penjualan');
            $table->index('id_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropIndex('nama_pelanggan');
        });
        Schema::table('produk', function (Blueprint $table) {
            $table->dropIndex('nama_produk');
            $table->dropIndex('stok');
        });
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropIndex('tanggal_penjualan');
            $table->dropIndex('id_pelanggan');
        });
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->dropIndex('id_penjualan');
            $table->dropIndex('id_produk');
        });
    }
};
