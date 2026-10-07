<?php

use App\Models\Penjualan;
use App\Models\User;

it('returns a successful response', function () {
    $this->get('/')->assertRedirect(route('login'));
});

it('formats the latest sale date on the dashboard', function () {
    $user = User::factory()->create();

    Penjualan::create([
        'tanggal_penjualan' => '2026-09-28 14:30:00',
        'id_pelanggan' => null,
        'total' => 25000,
        'pembayaran' => 30000,
        'kembalian' => 5000,
    ]);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('28-09-2026 14:30');
});
