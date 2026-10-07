@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')

    <div class="container-fluid">

        <div class="mb-4">

            <h2 class="fw-bold mb-1">
                Laporan Penjualan
            </h2>

            <p class="text-secondary mb-0">
                Lihat laporan transaksi penjualan
            </p>

        </div>


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Filter Laporan
                </h5>

                <form action="/laporan" method="GET">

                    <div class="row g-3 align-items-end">

                        <div class="col-md-3">

                            <label for="tanggal_mulai" class="form-label fw-semibold">
                                Tanggal Mulai
                            </label>

                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                                class="form-control">

                        </div>


                        <div class="col-md-3">

                            <label for="tanggal_selesai" class="form-label fw-semibold">
                                Tanggal Selesai
                            </label>

                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                                class="form-control">

                        </div>


                        <div class="col-md-3">

                            <label for="search" class="form-label fw-semibold">
                                Cari
                            </label>

                            <input type="search" id="search" name="search" value="{{ $search }}" class="form-control"
                                placeholder="Pelanggan atau tanggal, contoh: 17">

                        </div>

                        <div class="col-md-3">

                            <div class="d-flex gap-2">

                                <button type="submit" class="btn btn-primary">
                                    Tampilkan
                                </button>

                                <a href="/laporan" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <div class="row g-4 mb-4">

            <div class="col-md-6">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <p class="text-secondary mb-2">
                            Total Transaksi
                        </p>

                        <h2 class="fw-bold mb-0">
                            {{ $totalTransaksi }}
                        </h2>

                    </div>

                </div>

            </div>


            <div class="col-md-6">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <p class="text-secondary mb-2">
                            Total Pendapatan
                        </p>

                        <h2 class="fw-bold mb-0">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </h2>

                    </div>

                </div>

            </div>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h5 class="fw-bold mb-1">
                            Data Penjualan
                        </h5>

                        <p class="text-secondary mb-0">
                            Daftar transaksi berdasarkan filter
                        </p>

                    </div>


                    <a href="/laporan/cetak?tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}&search={{ urlencode($search) }}"
                        target="_blank" class="btn btn-success">

                        Cetak Laporan

                    </a>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Kembalian</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($penjualan as $item)

                                <tr>

                                    <td>
                                        {{ $penjualan->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $item->tanggal_penjualan }}
                                    </td>

                                    <td>
                                        {{ $item->pelanggan->nama_pelanggan ?? 'Umum' }}
                                    </td>

                                    <td class="fw-semibold">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($item->pembayaran, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($item->kembalian, 0, ',', '.') }}
                                    </td>

                                    <td>

                                        <a href="/penjualan/{{ $item->id }}/detail" class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center text-secondary py-5">

                                        Tidak ada transaksi pada periode ini.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $penjualan->links() }}
                </div>

            </div>

        </div>

    </div>

@endsection