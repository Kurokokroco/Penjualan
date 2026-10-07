@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')

    <div class="container-fluid">

        <div class="mb-4">

            <h2 class="fw-bold mb-1">
                Edit Produk
            </h2>

            <p class="text-secondary mb-0">
                Ubah informasi produk
            </p>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form action="/produk/{{ $produk->id }}" method="POST">

                    @csrf
                    @method('PUT')


                    <div class="mb-3">

                        <label for="kode_produk" class="form-label fw-semibold">
                            Kode Produk
                        </label>

                        <input type="text" id="kode_produk" name="kode_produk"
                            value="{{ old('kode_produk', $produk->kode_produk) }}"
                            class="form-control @error('kode_produk') is-invalid @enderror">

                        @error('kode_produk')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="mb-3">

                        <label for="nama_produk" class="form-label fw-semibold">
                            Nama Produk
                        </label>

                        <input type="text" id="nama_produk" name="nama_produk"
                            value="{{ old('nama_produk', $produk->nama_produk) }}"
                            class="form-control @error('nama_produk') is-invalid @enderror">

                        @error('nama_produk')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="mb-3">

                        <label for="harga" class="form-label fw-semibold">
                            Harga (Rp)
                        </label>

                        <input type="text" id="harga" name="harga" inputmode="numeric" autocomplete="off"
                            data-rupiah-input
                            value="{{ number_format((float) old('harga', $produk->harga), 0, ',', '.') }}"
                            class="form-control @error('harga') is-invalid @enderror">

                        @error('harga')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="mb-3">

                        <label for="stok" class="form-label fw-semibold">
                            Stok
                        </label>

                        <input type="number" id="stok" name="stok" min="0" value="{{ old('stok', $produk->stok) }}"
                            class="form-control @error('stok') is-invalid @enderror">

                        @error('stok')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="mb-4">

                        <label for="satuan" class="form-label fw-semibold">
                            Satuan
                        </label>

                        <select id="satuan" name="satuan" class="form-select @error('satuan') is-invalid @enderror">

                            <option value="">
                                -- Pilih Satuan --
                            </option>

                            <option value="pcs" {{ old('satuan', $produk->satuan) == 'pcs' ? 'selected' : '' }}>
                                Pcs
                            </option>

                            <option value="botol" {{ old('satuan', $produk->satuan) == 'botol' ? 'selected' : '' }}>
                                Botol
                            </option>

                            <option value="bungkus" {{ old('satuan', $produk->satuan) == 'bungkus' ? 'selected' : '' }}>
                                Bungkus
                            </option>

                            <option value="dus" {{ old('satuan', $produk->satuan) == 'dus' ? 'selected' : '' }}>
                                Dus
                            </option>

                        </select>

                        @error('satuan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="d-flex gap-2">

                        <a href="/produk" class="btn btn-secondary">
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script>
        const formatIDR = new Intl.NumberFormat('id-ID');
        const input = document.getElementById('harga');
        const form = document.getElementById('formProduk');

        input.addEventListener('input', () => {
            const angka = input.value.replace(/\D/g, '');
            input.value = angka ? formatIDR.format(angka) : '';
        });

        form.addEventListener('submit', () => {
            input.value = input.value.replace(/\D/g, '');   // "3.000" → "3000"
        });
    </script>   

@endsection