<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $produk = Produk::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('kode_produk', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('satuan', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('produk.index', compact('produk', 'search'));
    }

    public function create()
    {
        $kodeProduk = $this->nextProductCode();

        return view('produk.create', compact('kodeProduk'));
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);

        return view('produk.edit', compact('produk'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $request->merge([
            'harga' => $this->normalizePriceInput((string) $request->input('harga', '')),
        ]);

        $validated = $request->validate([
            'kode_produk' => 'required|unique:produk,kode_produk,'.$produk->id,
            'nama_produk' => 'required',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required',
        ]);

        $produk->update($validated);

        return redirect('/produk')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect('/produk')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function store(Request $request)
    {
        $request->merge([
            'kode_produk' => $this->nextProductCode(),
            'harga' => $this->normalizePriceInput((string) $request->input('harga', '')),
        ]);

        $validated = $request->validate([
            'kode_produk' => 'required|unique:produk,kode_produk',
            'nama_produk' => 'required',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required',
        ]);

        Produk::create($validated);

        return redirect('/produk');
    }

    private function normalizePriceInput(string $price): string
    {
        if (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $price) === 1) {
            return str_replace('.', '', $price);
        }

        return $price;
    }

    private function nextProductCode(): string
    {
        $existingCodes = Produk::query()
            ->where('kode_produk', 'like', 'PRD%')
            ->pluck('kode_produk')
            ->flip();

        for ($number = 1; ; $number++) {
            $code = 'PRD'.str_pad((string) $number, 3, '0', STR_PAD_LEFT);

            if (! $existingCodes->has($code)) {
                return $code;
            }
        }
    }
}
