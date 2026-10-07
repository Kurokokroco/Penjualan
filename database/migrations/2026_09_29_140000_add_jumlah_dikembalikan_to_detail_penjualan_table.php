<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table): void {
            $table->unsignedInteger('jumlah_dikembalikan')->default(0)->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table): void {
            $table->dropColumn('jumlah_dikembalikan');
        });
    }
};
