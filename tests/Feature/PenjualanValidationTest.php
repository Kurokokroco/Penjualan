<?php

namespace Tests\Feature;

use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Tests\TestCase;

class PenjualanValidationTest extends TestCase
{
    public function test_nonexistent_customer_is_rejected_when_adding_product_to_sale(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $produk = Produk::create([
            'kode_produk' => 'PRD-001',
            'nama_produk' => 'Produk Uji',
            'harga' => 10000,
            'stok' => 5,
            'satuan' => 'pcs',
        ]);

        $this->actingAs($user)
            ->from('/penjualan')
            ->post('/penjualan/tambah-produk', [
                'id_pelanggan' => 999999,
                'id_produk' => $produk->id,
                'jumlah' => 1,
            ])
            ->assertRedirect('/penjualan')
            ->assertSessionHasErrors('id_pelanggan');
    }

    public function test_sales_search_accepts_indonesian_date_format(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $tanggalPenjualan = now()->setTime(12, 0);
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Pelanggan Tanggal',
            'no_hp' => null,
            'alamat' => null,
        ]);

        Penjualan::create([
            'tanggal_penjualan' => $tanggalPenjualan,
            'id_pelanggan' => $pelanggan->id,
            'total' => 10000,
            'pembayaran' => 10000,
            'kembalian' => 0,
        ]);

        $this->actingAs($user)
            ->get('/penjualan?search=' . $tanggalPenjualan->format('d-m-Y'))
            ->assertOk()
            ->assertSee('Pelanggan Tanggal');

        $this->actingAs($user)
            ->get('/penjualan?search=' . $tanggalPenjualan->format('d'))
            ->assertOk()
            ->assertSee('Pelanggan Tanggal');
    }

    public function test_canceling_a_sale_clears_the_cart_and_selected_customer(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($user)
            ->withSession([
                'id_pelanggan' => 10,
                'keranjang' => [
                    1 => [
                        'id_produk' => 1,
                        'nama_produk' => 'Produk Uji',
                        'harga' => 10000,
                        'jumlah' => 1,
                        'subtotal' => 10000,
                    ],
                ],
            ])
            ->post('/penjualan/batal')
            ->assertRedirect('/penjualan')
            ->assertSessionHas('success', 'Keranjang berhasil dikosongkan.')
            ->assertSessionMissing('keranjang')
            ->assertSessionMissing('id_pelanggan');
    }

    public function test_checkout_rejects_a_cart_when_stock_has_changed(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Pelanggan Uji',
            'no_hp' => null,
            'alamat' => null,
        ]);
        $produk = Produk::create([
            'kode_produk' => 'PRD-001',
            'nama_produk' => 'Produk Uji',
            'harga' => 10000,
            'stok' => 2,
            'satuan' => 'pcs',
        ]);

        $this->actingAs($user)
            ->withSession([
                'id_pelanggan' => $pelanggan->id,
                'keranjang' => [
                    $produk->id => [
                        'id_produk' => $produk->id,
                        'nama_produk' => $produk->nama_produk,
                        'harga' => $produk->harga,
                        'jumlah' => 3,
                        'subtotal' => 30000,
                    ],
                ],
            ])
            ->from('/penjualan')
            ->post('/penjualan/simpan', ['pembayaran' => 30000])
            ->assertRedirect('/penjualan')
            ->assertSessionHas('error', 'Stok Produk Uji tidak mencukupi.');

        $this->assertDatabaseCount('penjualan', 0);
        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'stok' => 2,
        ]);
    }

    public function test_checkout_saves_sale_and_deducts_available_stock(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Pelanggan Uji',
            'no_hp' => null,
            'alamat' => null,
        ]);
        $produk = Produk::create([
            'kode_produk' => 'PRD-001',
            'nama_produk' => 'Produk Uji',
            'harga' => 10000,
            'stok' => 5,
            'satuan' => 'pcs',
        ]);

        $this->actingAs($user)
            ->withSession([
                'id_pelanggan' => $pelanggan->id,
                'keranjang' => [
                    $produk->id => [
                        'id_produk' => $produk->id,
                        'nama_produk' => $produk->nama_produk,
                        'harga' => $produk->harga,
                        'jumlah' => 3,
                        'subtotal' => 30000,
                    ],
                ],
            ])
            ->post('/penjualan/simpan', ['pembayaran' => 30000])
            ->assertRedirect('/penjualan')
            ->assertSessionHas('success', 'Transaksi berhasil disimpan. Member memperoleh 1 poin.');

        $this->assertDatabaseCount('penjualan', 1);
        $this->assertDatabaseCount('detail_penjualan', 1);
        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'stok' => 2,
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'poin' => 1,
        ]);
    }

    public function test_member_reaches_discount_tier_after_fifth_point_and_earns_points_after_checkout(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Member Poin',
            'no_hp' => null,
            'alamat' => null,
        ]);
        $pelanggan->forceFill(['poin' => 4])->save();
        $produk = Produk::create([
            'kode_produk' => 'PRD-004',
            'nama_produk' => 'Produk Poin',
            'harga' => 10000,
            'stok' => 5,
            'satuan' => 'pcs',
        ]);

        $this->actingAs($user)
            ->withSession([
                'id_pelanggan' => $pelanggan->id,
                'keranjang' => [
                    $produk->id => [
                        'id_produk' => $produk->id,
                        'nama_produk' => $produk->nama_produk,
                        'harga' => $produk->harga,
                        'jumlah' => 1,
                        'subtotal' => 10000,
                    ],
                ],
            ])
            ->post('/penjualan/simpan', ['pembayaran' => 10000])
            ->assertRedirect('/penjualan')
            ->assertSessionHas('success', 'Transaksi berhasil disimpan. Member memperoleh 1 poin.');

        $this->assertDatabaseHas('penjualan', [
            'id_pelanggan' => $pelanggan->id,
            'total_sebelum_diskon' => 10000,
            'diskon_persen' => 0,
            'diskon' => 0,
            'total' => 10000,
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'poin' => 5,
        ]);

        $this->withSession([
            'id_pelanggan' => $pelanggan->id,
            'keranjang' => [
                $produk->id => [
                    'id_produk' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => $produk->harga,
                    'jumlah' => 1,
                    'subtotal' => 10000,
                ],
            ],
        ])
            ->post('/penjualan/simpan', ['pembayaran' => 9900])
            ->assertRedirect('/penjualan')
            ->assertSessionHas('success', 'Transaksi berhasil disimpan. Member memperoleh 1 poin.');

        $this->assertDatabaseHas('penjualan', [
            'id_pelanggan' => $pelanggan->id,
            'total_sebelum_diskon' => 10000,
            'diskon_persen' => 1,
            'diskon' => 100,
            'total' => 9900,
            'pembayaran' => 9900,
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'poin' => 6,
        ]);
    }

    public function test_member_points_are_not_awarded_when_payment_is_below_discounted_total(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Member Poin',
            'no_hp' => null,
            'alamat' => null,
        ]);
        $pelanggan->forceFill(['poin' => 5])->save();
        $produk = Produk::create([
            'kode_produk' => 'PRD-005',
            'nama_produk' => 'Produk Diskon',
            'harga' => 10000,
            'stok' => 5,
            'satuan' => 'pcs',
        ]);

        $this->actingAs($user)
            ->withSession([
                'id_pelanggan' => $pelanggan->id,
                'keranjang' => [
                    $produk->id => [
                        'id_produk' => $produk->id,
                        'nama_produk' => $produk->nama_produk,
                        'harga' => $produk->harga,
                        'jumlah' => 1,
                        'subtotal' => 10000,
                    ],
                ],
            ])
            ->from('/penjualan')
            ->post('/penjualan/simpan', ['pembayaran' => 9899])
            ->assertRedirect('/penjualan')
            ->assertSessionHas('error', 'Pembayaran kurang. Total setelah diskon adalah Rp 9.900.');

        $this->assertDatabaseCount('penjualan', 0);
        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'poin' => 5,
        ]);
    }

    public function test_checkout_saves_walk_in_sale_without_a_member(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $produk = Produk::create([
            'kode_produk' => 'PRD-002',
            'nama_produk' => 'Produk Umum',
            'harga' => 15000,
            'stok' => 5,
            'satuan' => 'pcs',
        ]);

        $this->actingAs($user)
            ->withSession([
                'id_pelanggan' => null,
                'keranjang' => [
                    $produk->id => [
                        'id_produk' => $produk->id,
                        'nama_produk' => $produk->nama_produk,
                        'harga' => $produk->harga,
                        'jumlah' => 1,
                        'subtotal' => 15000,
                    ],
                ],
            ])
            ->from('/penjualan')
            ->post('/penjualan/simpan', ['pembayaran' => 15000])
            ->assertRedirect('/penjualan')
            ->assertSessionHas('success', 'Transaksi berhasil disimpan.');

        $this->assertDatabaseHas('penjualan', [
            'id_pelanggan' => null,
            'total' => 15000,
        ]);
    }

    public function test_returning_damaged_items_increases_stock_and_cannot_exceed_sold_quantity(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Member Retur',
            'no_hp' => null,
            'alamat' => null,
        ]);
        $pelanggan->forceFill(['poin' => 5])->save();
        $produk = Produk::create([
            'kode_produk' => 'PRD-003',
            'nama_produk' => 'Produk Rusak',
            'harga' => 20000,
            'stok' => 5,
            'satuan' => 'pcs',
        ]);

        $this->actingAs($user)
            ->withSession([
                'id_pelanggan' => $pelanggan->id,
                'keranjang' => [
                    $produk->id => [
                        'id_produk' => $produk->id,
                        'nama_produk' => $produk->nama_produk,
                        'harga' => $produk->harga,
                        'jumlah' => 2,
                        'subtotal' => 40000,
                    ],
                ],
            ])
            ->post('/penjualan/simpan', ['pembayaran' => 39600])
            ->assertRedirect('/penjualan')
            ->assertSessionHas('success', 'Transaksi berhasil disimpan. Member memperoleh 1 poin.');

        $penjualan = Penjualan::query()->latest('id')->firstOrFail();
        $detail = DetailPenjualan::query()->firstOrFail();

        $this->actingAs($user)
            ->post("/penjualan/{$penjualan->id}/detail/{$detail->id}/retur", [
                'jumlah_retur' => 1,
            ])
            ->assertRedirect("/penjualan/{$penjualan->id}/detail")
            ->assertSessionHas('success', 'Retur berhasil diproses dan stok telah ditambahkan.');

        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'stok' => 4,
        ]);
        $this->assertDatabaseHas('detail_penjualan', [
            'id' => $detail->id,
            'jumlah_dikembalikan' => 1,
        ]);
        $this->assertDatabaseHas('penjualan', [
            'id' => $penjualan->id,
            'total_sebelum_diskon' => 20000,
            'diskon_persen' => 1,
            'diskon' => 200,
            'total' => 19800,
            'pembayaran' => 0,
            'kembalian' => 0,
        ]);

        $this->actingAs($user)
            ->post("/penjualan/{$penjualan->id}/pembayaran", [
                'pembayaran' => 25000,
            ])
            ->assertRedirect("/penjualan/{$penjualan->id}/detail")
            ->assertSessionHas('success', 'Pembayaran dan kembalian berhasil diperbarui.');

        $this->assertDatabaseHas('penjualan', [
            'id' => $penjualan->id,
            'total' => 19800,
            'pembayaran' => 25000,
            'kembalian' => 5200,
        ]);

        $this->actingAs($user)
            ->post("/penjualan/{$penjualan->id}/detail/{$detail->id}/retur", [
                'jumlah_retur' => 2,
            ])
            ->assertRedirect("/penjualan/{$penjualan->id}/detail")
            ->assertSessionHas('error', 'Jumlah retur melebihi jumlah produk yang masih dapat diretur.');

        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'stok' => 4,
        ]);
    }
}
