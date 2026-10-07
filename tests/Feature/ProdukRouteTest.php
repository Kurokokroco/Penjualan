<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\User;
use Tests\TestCase;

class ProdukRouteTest extends TestCase
{
    public function test_guest_is_redirected_from_create_produk_page(): void
    {
        $this->get('/produk/create')->assertRedirect(route('login'));
    }

    public function test_kasir_cannot_access_create_produk_page(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($user)
            ->get('/produk/create')
            ->assertForbidden();
    }

    public function test_admin_can_access_create_produk_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/produk/create');

        $response->assertOk();
        $response->assertViewIs('produk.create');
    }

    public function test_admin_sees_next_available_product_code_on_create_form(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Produk::create([
            'kode_produk' => 'PRD001',
            'nama_produk' => 'Produk Pertama',
            'harga' => 1000,
            'stok' => 10,
            'satuan' => 'pcs',
        ]);
        Produk::create([
            'kode_produk' => 'PRD003',
            'nama_produk' => 'Produk Ketiga',
            'harga' => 1000,
            'stok' => 10,
            'satuan' => 'pcs',
        ]);

        $response = $this->actingAs($user)->get('/produk/create');

        $response->assertSee('value="PRD002"', false);
    }

    public function test_admin_cannot_store_product_with_negative_price_or_stock(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->post('/produk', [
                'kode_produk' => 'PRD-001',
                'nama_produk' => 'Produk Uji',
                'harga' => -1000,
                'stok' => -1,
                'satuan' => 'pcs',
            ])
            ->assertSessionHasErrors(['harga', 'stok']);

        $this->assertDatabaseCount('produk', 0);
    }

    public function test_admin_can_store_product_with_thousands_separator_in_price(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post('/produk', [
            'nama_produk' => 'Produk Uji',
            'harga' => '250.000',
            'stok' => 10,
            'satuan' => 'pcs',
        ]);

        $response->assertRedirect('/produk');
        $this->assertDatabaseHas('produk', [
            'kode_produk' => 'PRD001',
            'harga' => 250000,
        ]);
    }

    public function test_admin_stores_product_with_the_next_available_code(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Produk::create([
            'kode_produk' => 'PRD001',
            'nama_produk' => 'Produk Pertama',
            'harga' => 1000,
            'stok' => 10,
            'satuan' => 'pcs',
        ]);
        Produk::create([
            'kode_produk' => 'PRD003',
            'nama_produk' => 'Produk Ketiga',
            'harga' => 1000,
            'stok' => 10,
            'satuan' => 'pcs',
        ]);

        $response = $this->actingAs($user)->post('/produk', [
            'nama_produk' => 'Produk Kedua',
            'harga' => 2000,
            'stok' => 10,
            'satuan' => 'pcs',
        ]);

        $response->assertRedirect('/produk');
        $this->assertDatabaseHas('produk', [
            'kode_produk' => 'PRD002',
            'nama_produk' => 'Produk Kedua',
        ]);
    }

    public function test_admin_can_update_product_with_thousands_separator_in_price(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Produk::create([
            'kode_produk' => 'PRD-003',
            'nama_produk' => 'Produk Uji',
            'harga' => 1000,
            'stok' => 10,
            'satuan' => 'pcs',
        ]);

        $response = $this->actingAs($user)->put('/produk/'.$product->id, [
            'kode_produk' => 'PRD-003',
            'nama_produk' => 'Produk Uji',
            'harga' => '1.250.000',
            'stok' => 10,
            'satuan' => 'pcs',
        ]);

        $response->assertRedirect('/produk');
        $this->assertDatabaseHas('produk', [
            'id' => $product->id,
            'harga' => 1250000,
        ]);
    }
}
