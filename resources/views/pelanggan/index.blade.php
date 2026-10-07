@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="fw-bold mb-1">
                    Pelanggan
                </h2>

                <p class="text-secondary mb-0">
                    Kelola data pelanggan minimarket/Member
                </p>
            </div>

            <a href="/pelanggan/create" class="btn btn-primary">
                + Tambah Pelanggan
            </a>

        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" action="/pelanggan" class="d-flex gap-2 mb-3">
            <input type="search" name="search" value="{{ $search }}" class="form-control"
                placeholder="Cari nama, nomor HP, atau alamat">
            <button type="submit" class="btn btn-primary">Cari</button>
            @if ($search !== '')
                <a href="/pelanggan" class="btn btn-secondary">Reset</a>
            @endif
        </form>


        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th width="60">No</th>
                                <th>Nama Pelanggan</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
                                <th>Poin / Diskon</th>
                                <th width="170">Aksi</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($pelanggan as $item)

                                <tr>

                                    <td>
                                        {{ $pelanggan->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $item->nama_pelanggan }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $item->no_hp ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->alamat ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="fw-semibold">{{ $item->poin }} poin</span>
                                        <small class="d-block text-secondary">Diskon {{ $item->diskonPersen() }}%</small>
                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <a href="/pelanggan/{{ $item->id }}/edit" class="btn btn-sm btn-warning">
                                                Edit
                                            </a>

                                            <form action="/pelanggan/{{ $item->id }}" method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                                    Hapus
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center text-secondary py-5">

                                        Belum ada data pelanggan.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="mt-3">
            {{ $pelanggan->links() }}
        </div>

    </div>

@endsection