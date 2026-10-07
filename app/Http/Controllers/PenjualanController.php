<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::all();
        $pelanggan = Pelanggan::all();
        $search = trim((string) $request->input('search', ''));
        $tanggalPencarian = $this->normalizeSearchDate($search);
        $hariPencarian = $this->normalizeSearchDay($search);

        $penjualan = Penjualan::query()
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

        return view('penjualan.index', compact('produk', 'pelanggan', 'penjualan', 'search'));
    }

    public function tambahProduk(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'nullable|exists:pelanggan,id',
            'id_produk' => 'required|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $produk = Produk::findOrFail($request->id_produk);

        $keranjang = session()->get('keranjang', []);

        $idProduk = $produk->id;
        $jumlahDiKeranjang = $keranjang[$idProduk]['jumlah'] ?? 0;

        if ($jumlahDiKeranjang + $request->jumlah > $produk->stok) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        if (isset($keranjang[$idProduk])) {
            $keranjang[$idProduk]['jumlah'] += $request->jumlah;
        } else {
            $keranjang[$idProduk] = [
                'id_produk' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'harga' => $produk->harga,
                'jumlah' => $request->jumlah,
                'subtotal' => $produk->harga * $request->jumlah,
            ];
        }

        $keranjang[$idProduk]['subtotal'] =
            $keranjang[$idProduk]['harga'] * $keranjang[$idProduk]['jumlah'];

        session()->put('keranjang', $keranjang);
        session()->put('id_pelanggan', $request->id_pelanggan);

        return redirect('/penjualan');
    }

    public function batal()
    {
        session()->forget(['keranjang', 'id_pelanggan']);

        return redirect('/penjualan')
            ->with('success', 'Keranjang berhasil dikosongkan.');
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'pembayaran' => 'required|numeric|min:1',
        ]);

        $keranjang = session()->get('keranjang', []);
        $idPelanggan = session()->get('id_pelanggan');

        if (empty($keranjang)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        $subtotal = array_sum(array_column($keranjang, 'subtotal'));
        $pembayaran = (float) $request->input('pembayaran');

        $checkoutError = DB::transaction(function () use (
            $keranjang,
            $idPelanggan,
            $subtotal,
            $pembayaran
        ): ?string {
            $pelanggan = $idPelanggan === null
                ? null
                : Pelanggan::query()->lockForUpdate()->find($idPelanggan);

            if ($idPelanggan !== null && ! $pelanggan) {
                return 'Member tidak ditemukan.';
            }

            $diskonPersen = $pelanggan?->diskonPersen() ?? 0;
            $diskon = (int) round($subtotal * $diskonPersen / 100);
            $total = max(0, $subtotal - $diskon);

            if ($pembayaran < $total) {
                return 'Pembayaran kurang. Total setelah diskon adalah Rp ' . number_format($total, 0, ',', '.') . '.';
            }

            $kembalian = $pembayaran - $total;

            $produkTersedia = Produk::query()
                ->whereIn('id', array_column($keranjang, 'id_produk'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($keranjang as $item) {
                $produk = $produkTersedia->get($item['id_produk']);

                if (! $produk) {
                    return 'Produk di keranjang sudah tidak tersedia.';
                }

                if ($item['jumlah'] > $produk->stok) {
                    return 'Stok ' . $produk->nama_produk . ' tidak mencukupi.';
                }
            }

            $penjualan = Penjualan::create([
                'tanggal_penjualan' => now(),
                'id_pelanggan' => $idPelanggan,
                'total_sebelum_diskon' => $subtotal,
                'diskon_persen' => $diskonPersen,
                'diskon' => $diskon,
                'total' => $total,
                'pembayaran' => $pembayaran,
                'kembalian' => $kembalian,
            ]);

            foreach ($keranjang as $item) {
                $produk = $produkTersedia->get($item['id_produk']);

                $penjualan->detailPenjualan()->create([
                    'id_produk' => $produk->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);

                $produk->decrement('stok', $item['jumlah']);
            }

            $pelanggan?->increment('poin');

            return null;
        });

        if ($checkoutError !== null) {
            return back()->with('error', $checkoutError);
        }

        session()->forget('keranjang');
        session()->forget('id_pelanggan');

        return redirect('/penjualan')
            ->with('success', $idPelanggan === null
                ? 'Transaksi berhasil disimpan.'
                : 'Transaksi berhasil disimpan. Member memperoleh 1 poin.');
    }

    public function detail($id)
    {
        $search = trim((string) request('search', ''));
        $penjualan = Penjualan::with([
            'pelanggan',
        ])->findOrFail($id);
        $detailPenjualan = $penjualan->detailPenjualan()
            ->with('produk')
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('produk', function ($query) use ($search): void {
                    $query->where('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produk', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('penjualan.detail', compact('penjualan', 'detailPenjualan', 'search'));
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

    public function retur(Request $request, $id, $detailId)
    {
        $request->validate([
            'jumlah_retur' => 'required|integer|min:1',
        ]);

        $jumlahRetur = (int) $request->input('jumlah_retur');

        $returnError = DB::transaction(function () use ($id, $detailId, $jumlahRetur): ?string {
            $penjualan = Penjualan::query()
                ->lockForUpdate()
                ->find($id);

            if (! $penjualan) {
                return 'Transaksi tidak ditemukan.';
            }

            $detail = DetailPenjualan::query()
                ->where('id_penjualan', $id)
                ->whereKey($detailId)
                ->lockForUpdate()
                ->first();

            if (! $detail) {
                return 'Detail produk tidak ditemukan dalam transaksi ini.';
            }

            $jumlahTersisa = $detail->jumlah - $detail->jumlah_dikembalikan;

            if ($jumlahRetur > $jumlahTersisa) {
                return 'Jumlah retur melebihi jumlah produk yang masih dapat diretur.';
            }

            $produk = Produk::query()
                ->lockForUpdate()
                ->find($detail->id_produk);

            if (! $produk) {
                return 'Produk retur tidak ditemukan.';
            }

            $nilaiRetur = $detail->harga * $jumlahRetur;
            $subtotalSisa = max(0, $penjualan->total_sebelum_diskon - $nilaiRetur);
            $diskonSisa = (int) round($subtotalSisa * $penjualan->diskon_persen / 100);

            $detail->increment('jumlah_dikembalikan', $jumlahRetur);
            $produk->increment('stok', $jumlahRetur);
            $penjualan->update([
                'total_sebelum_diskon' => $subtotalSisa,
                'diskon' => $diskonSisa,
                'total' => max(0, $subtotalSisa - $diskonSisa),
                'pembayaran' => 0,
                'kembalian' => 0,
            ]);

            return null;
        });

        if ($returnError !== null) {
            return redirect("/penjualan/{$id}/detail")
                ->with('error', $returnError);
        }

        return redirect("/penjualan/{$id}/detail")
            ->with('success', 'Retur berhasil diproses dan stok telah ditambahkan.');
    }

    public function pembayaran(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        $request->validate([
            'pembayaran' => 'required|numeric|min:' . $penjualan->total,
        ]);

        $pembayaran = (float) $request->input('pembayaran');
        $kembalian = $pembayaran - (float) $penjualan->total;

        $penjualan->update([
            'pembayaran' => $pembayaran,
            'kembalian' => $kembalian,
        ]);

        return redirect("/penjualan/{$id}/detail")
            ->with('success', 'Pembayaran dan kembalian berhasil diperbarui.');
    }

    public function laporan(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        $search = trim((string) $request->input('search', ''));
        $tanggalPencarian = $this->normalizeSearchDate($search);
        $hariPencarian = $this->normalizeSearchDay($search);

        $query = Penjualan::with('pelanggan')
            ->latest('tanggal_penjualan');

        if ($tanggalMulai) {
            $query->whereDate('tanggal_penjualan', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai) {
            $query->whereDate('tanggal_penjualan', '<=', $tanggalSelesai);
        }

        if ($search !== '') {
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
        }

        $totalTransaksi = (clone $query)->count();
        $totalPendapatan = (clone $query)->sum('total');

        $penjualan = $query->paginate(10)->withQueryString();

        return view('laporan.index', compact(
            'penjualan',
            'tanggalMulai',
            'tanggalSelesai',
            'search',
            'totalTransaksi',
            'totalPendapatan'
        ));
    }

    public function cetakLaporan(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        $search = trim((string) $request->input('search', ''));
        $tanggalPencarian = $this->normalizeSearchDate($search);
        $hariPencarian = $this->normalizeSearchDay($search);

        $query = Penjualan::with('pelanggan')
            ->latest('tanggal_penjualan');

        if ($tanggalMulai) {
            $query->whereDate('tanggal_penjualan', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai) {
            $query->whereDate('tanggal_penjualan', '<=', $tanggalSelesai);
        }

        if ($search !== '') {
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
        }

        $penjualan = $query->get();

        $totalTransaksi = $penjualan->count();
        $totalPendapatan = $penjualan->sum('total');

        return view('laporan.cetak', compact(
            'penjualan',
            'tanggalMulai',
            'tanggalSelesai',
            'totalTransaksi',
            'totalPendapatan'
        ));
    }
}
