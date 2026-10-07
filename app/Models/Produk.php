<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'harga',
        'stok',
        'satuan',
    ];

    public function DetailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'id_produk');
    }
}
