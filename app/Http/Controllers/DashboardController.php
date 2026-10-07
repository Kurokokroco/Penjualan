<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProduk = Produk::count();
        $totalPelanggan = Pelanggan::count();
        $totalPenjualan = Penjualan::count();

        $search = trim((string) $request->input('search', ''));
        $tanggalPencarian = $this->normalizeSearchDate($search);
        $hariPencarian = $this->normalizeSearchDay($search);

        $penjualanTerbaru = Penjualan::query()
            ->with('pelanggan')
            ->when($search !== '', function ($query) use ($search, $tanggalPencarian, $hariPencarian): void {
                $query->where(function ($query) use ($search, $tanggalPencarian, $hariPencarian): void {
                    if ($tanggalPencarian !== null) {
                        $query->whereDate('tanggal_penjualan', $tanggalPencarian);
                    } elseif ($hariPencarian !== null) {
                        $query->whereDay('tanggal_penjualan', $hariPencarian);
                    } else {
                        $query->whereHas('pelanggan', function ($query) use ($search): void {
                            $query->where('nama_pelanggan', 'like', "%{$search}%");
                        });
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard', compact(
            'totalProduk',
            'totalPelanggan',
            'totalPenjualan',
            'penjualanTerbaru',
            'search'
        ));
    }

    private function normalizeSearchDate(string $search): ?string
    {
        foreach (['Y-m-d', 'd-m-Y', 'd/m/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $search)->format('Y-m-d');
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function normalizeSearchDay(string $search): ?int
    {
        if (! preg_match('/^(?:0?[1-9]|[12][0-9]|3[01])$/', $search)) {
            return null;
        }

        return (int) $search;
    }
}
