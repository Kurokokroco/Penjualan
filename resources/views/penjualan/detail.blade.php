@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="fw-bold mb-1">
                    Detail Penjualan
                </h2>

                <p class="text-secondary mb-0">
                    Informasi lengkap transaksi penjualan
                </p>
            </div>

            <a href="/penjualan" class="btn btn-secondary">
                ← Kembali
            </a>

        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        <div class="row g-4">


            <div class="col-lg-4">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <h5 class="fw-bold mb-4">
                            Informasi Transaksi
                        </h5>


                        <div class="mb-3">

                            <small class="text-secondary">
                                Nomor Transaksi
                            </small>

                            <div class="fw-semibold">
                                #{{ $penjualan->id }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-secondary">
                                Tanggal
                            </small>

                            <div class="fw-semibold">
                                {{ $penjualan->tanggal_penjualan }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-secondary">
                                Pelanggan
                            </small>

                            <div class="fw-semibold">
                                {{ $penjualan->pelanggan->nama_pelanggan ?? 'Umum' }}
                            </div>

                        </div>


                        <hr>


                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Total
                            </span>

                            <span class="fw-bold">
                                Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                            </span>

                        </div>


                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Pembayaran
                            </span>

                            <span>
                                Rp {{ number_format($penjualan->pembayaran, 0, ',', '.') }}
                            </span>

                        </div>


                        <div class="d-flex justify-content-between">

                            <span>
                                Kembalian
                            </span>

                            <span class="text-success fw-bold">
                                Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}
                            </span>

                        </div>

                        @if ($penjualan->total > 0)
                            <hr>

                            <form action="/penjualan/{{ $penjualan->id }}/pembayaran" method="POST">
                                @csrf

                                <label for="detail_pembayaran_display" class="form-label fw-semibold">
                                    Pembayaran Baru
                                </label>

                                <input type="text" id="detail_pembayaran_display"
                                    value="{{ old('pembayaran', $penjualan->pembayaran > 0 ? number_format($penjualan->pembayaran, 0, ',', '.') : '') }}"
                                    inputmode="numeric" class="form-control @error('pembayaran') is-invalid @enderror"
                                    placeholder="Masukkan pembayaran" autocomplete="off">

                                <input type="hidden" name="pembayaran" id="detail_pembayaran"
                                    value="{{ old('pembayaran', $penjualan->pembayaran > 0 ? $penjualan->pembayaran : '') }}">

                                @error('pembayaran')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <button type="submit" class="btn btn-success w-100 mt-3">
                                    Simpan Pembayaran
                                </button>
                            </form>
                        @endif

                    </div>

                </div>

            </div>



            <div class="col-lg-8">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <h5 class="fw-bold mb-4">
                            Produk yang Dibeli
                        </h5>

                        <form method="GET" action="/penjualan/{{ $penjualan->id }}/detail" class="d-flex gap-2 mb-3">
                            <input type="search" name="search" value="{{ $search }}" class="form-control"
                                placeholder="Cari nama atau kode produk">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if ($search !== '')
                                <a href="/penjualan/{{ $penjualan->id }}/detail" class="btn btn-secondary">Reset</a>
                            @endif
                        </form>


                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead class="table-light">

                                    <tr>
                                        <th>No</th>
                                        <div class="d-flex justify-content-between mb-2">

                                            <span>
                                                Subtotal
                                            </span>

                                            <span>
                                                Rp {{ number_format($penjualan->total_sebelum_diskon, 0, ',', '.') }}
                                            </span>

                                        </div>


                                        <div class="d-flex justify-content-between mb-2">

                                            <span>
                                                Diskon member ({{ $penjualan->diskon_persen }}%)
                                            </span>

                                            <span>
                                                - Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}
                                            </span>

                                        </div>


                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Retur</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>

                                </thead>


                                <tbody>

                                    @forelse ($detailPenjualan as $item)

                                        <tr>

                                            <td>
                                                {{ $detailPenjualan->firstItem() + $loop->index }}
                                            </td>

                                            <td>

                                                <div class="fw-semibold">
                                                    {{ $item->produk->nama_produk }}
                                                </div>

                                                <small class="text-secondary">
                                                    {{ $item->produk->kode_produk }}
                                                </small>

                                            </td>

                                            <td>
                                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                {{ $item->jumlah }}
                                            </td>

                                            <td>
                                                {{ $item->jumlah_dikembalikan }}
                                            </td>

                                            <td class="fw-semibold">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                @php
                                                    $jumlahTersisa = $item->jumlah - $item->jumlah_dikembalikan;
                                                @endphp

                                                @if ($jumlahTersisa > 0)
                                                    <form action="/penjualan/{{ $penjualan->id }}/detail/{{ $item->id }}/retur"
                                                        method="POST" class="d-flex gap-2">
                                                        @csrf
                                                        <input type="number" name="jumlah_retur"
                                                            class="form-control form-control-sm" min="1" max="{{ $jumlahTersisa }}"
                                                            value="1" style="width: 80px" required>
                                                        <button type="submit" class="btn btn-sm btn-warning"
                                                            onclick="return confirm('Proses retur barang ini?')">
                                                            Retur
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-secondary">Selesai</span>
                                                @endif
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="7" class="text-center text-secondary py-5">

                                                Tidak ada detail produk.

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>


                                <tfoot>

                                    <tr>

                                        <th colspan="6" class="text-end">

                                            Total

                                        </th>

                                        <th class="fw-bold">

                                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}

                                        </th>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                        <div class="mt-3">
                            {{ $detailPenjualan->links() }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        const detailPaymentDisplay = document.getElementById('detail_pembayaran_display');
        const detailPaymentValue = document.getElementById('detail_pembayaran');

        if (detailPaymentDisplay && detailPaymentValue) {
            detailPaymentDisplay.addEventListener('input', (event) => {
                const digits = event.target.value.replace(/\D/g, '');

                detailPaymentValue.value = digits;
                event.target.value = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            });
        }
    </script>
@endpush