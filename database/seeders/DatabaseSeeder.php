<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Minimarket',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir Minimarket',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );

        $pelanggan = collect();

        for ($index = 1; $index <= 40; $index++) {
            $pelanggan->push(Pelanggan::create([
                'nama_pelanggan' => 'Member Dummy '.$index,
                'no_hp' => '0812'.str_pad((string) $index, 8, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Contoh Nomor '.$index,
            ]));
        }

        $produk = collect();

        for ($index = 1; $index <= 50; $index++) {
            $produk->push(Produk::create([
                'kode_produk' => 'DUMMY-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'nama_produk' => 'Produk Dummy '.$index,
                'harga' => random_int(5000, 150000),
                'stok' => random_int(50, 200),
                'satuan' => ['pcs', 'botol', 'kotak', 'bungkus'][random_int(0, 3)],
            ]));
        }

        for ($index = 1; $index <= 60; $index++) {
            $tanggalPenjualan = now()->subDays(random_int(0, 90));
            $produkTransaksi = $produk->random(random_int(1, 3));
            $total = 0;
            $detail = [];

            foreach ($produkTransaksi as $item) {
                $jumlah = random_int(1, 5);
                $subtotal = $item->harga * $jumlah;
                $total += $subtotal;
                $detail[] = [
                    'produk' => $item,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }

            $pembayaran = (int) (ceil($total / 10000) * 10000);

            $penjualan = Penjualan::create([
                'tanggal_penjualan' => $tanggalPenjualan,
                'id_pelanggan' => $index % 5 === 0 ? null : $pelanggan->random()->id,
                'total' => $total,
                'pembayaran' => $pembayaran,
                'kembalian' => $pembayaran - $total,
            ]);

            foreach ($detail as $item) {
                $penjualan->detailPenjualan()->create([
                    'id_produk' => $item['produk']->id,
                    'jumlah' => $item['jumlah'],
                    'jumlah_dikembalikan' => 0,
                    'harga' => $item['produk']->harga,
                    'subtotal' => $item['subtotal'],
                ]);

                $item['produk']->decrement('stok', $item['jumlah']);
            }
        }

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );
    }
}
