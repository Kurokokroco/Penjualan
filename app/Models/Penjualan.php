<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'tanggal_penjualan',
        'id_pelanggan',
        'total',
        'total_sebelum_diskon',
        'diskon_persen',
        'diskon',
        'pembayaran',
        'kembalian',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penjualan' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan');
    }
}
