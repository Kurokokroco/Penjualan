<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table): void {
            $table->unsignedInteger('poin')->default(0);
        });

        Schema::table('penjualan', function (Blueprint $table): void {
            $table->decimal('total_sebelum_diskon', 12, 2)->default(0)->after('total');
            $table->unsignedTinyInteger('diskon_persen')->default(0)->after('total_sebelum_diskon');
            $table->decimal('diskon', 12, 2)->default(0)->after('diskon_persen');
        });

        DB::table('penjualan')->update([
            'total_sebelum_diskon' => DB::raw('total'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table): void {
            $table->dropColumn(['total_sebelum_diskon', 'diskon_persen', 'diskon']);
        });

        Schema::table('pelanggan', function (Blueprint $table): void {
            $table->dropColumn('poin');
        });
    }
};
