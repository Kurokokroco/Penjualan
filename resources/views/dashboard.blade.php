@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary mb-2">
                        Total Produk
                    </div>

                    <h2 class="fw-bold mb-0">
                        {{ $totalProduk }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary mb-2">
                        Total Pelanggan
                    </div>

                    <h2 class="fw-bold mb-0">
                        {{ $totalPelanggan }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary mb-2">
                        Total Penjualan
                    </div>

                    <h2 class="fw-bold mb-0">
                        {{ $totalPenjualan }}
                    </h2>
                </div>
            </div>
        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <h5 class="fw-bold mb-3">
                Penjualan Terbaru
            </h5>

            <form method="GET" action="/dashboard" class="d-flex gap-2 mb-3">
                <input type="search"
                       name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Cari pelanggan atau tanggal, contoh: 17">
                <button type="submit" class="btn btn-primary">Cari</button>
                @if ($search !== '')
                    <a href="/dashboard" class="btn btn-secondary">Reset</a>
                @endif
            </form>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($penjualanTerbaru as $item)

                            <tr>
                                <td>{{ $penjualanTerbaru->firstItem() + $loop->index }}</td>

                                <td>
                                    {{ $item->tanggal_penjualan->format('d-m-Y H:i') }}
                                </td>

                                <td>
                                    {{ $item->pelanggan->nama_pelanggan ?? 'Umum' }}
                                </td>

                                <td>
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">
                                    Belum ada transaksi.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $penjualanTerbaru->links() }}
            </div>

        </div>

    </div>

@endsection