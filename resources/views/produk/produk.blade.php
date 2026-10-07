@extends('layouts.app')
@section('title', 'Produk')
@section('content')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="fw-bold mb-1">Produk</h2>

                <p class="text-secondary mb-0">
                    Kelola Data Minimarket
                </p>
            </div>

            <a href="/produk/create" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Tambah Produk
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">No</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th width="170">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produk as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="f  w-semibold">{{ $item->kode_produk }}</span></td>
                                    <td>{{ $item->nama_produk }}</td>
                                    <td class="text-nowrap">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="text-nowrap">
                                        @if ($item->stok > 0)
                                            <span class="badge text-bg-success">
                                                {{ $item->stok }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-danger">
                                                Habis
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="/produk/{{ $item->id }}/edit" class="btn btn-sm btn-warning">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-secondary py-4">
                                        Belum ada data produk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


@endsection