@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')

    <div class="container-fluid">

        <div class="mb-4">

            <h2 class="fw-bold mb-1">
                Tambah Produk
            </h2>

            <p class="text-secondary mb-0">
                Tambahkan produk baru ke dalam sistem
            </p>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form action="/produk" method="POST">

                    @csrf


                    <div class="mb-3">

                        <label for="kode_produk" class="form-label fw-semibold">
                            Kode Produk
                        </label>

                        <input type="text" id="kode_produk" name="kode_produk" value="{{ $kodeProduk }}" readonly
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

                        <input type="text" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}"
                            class="form-control @error('nama_produk') is-invalid @enderror"
                            placeholder="Contoh: Indomie Goreng">

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
                            data-rupiah-input value="{{ old('harga') === null ? '' : number_format((float) old('harga'), 0, ',', '.') }}"
                            class="form-control @error('harga') is-invalid @enderror"
                            placeholder="Contoh: 2.500">

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

                        <input type="text" id="stok" name="stok" min="0" value="{{ old('stok') }}"
                            class="form-control @error('stok') is-invalid @enderror" placeholder="Contoh: 50">

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

                            <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>
                                Pcs
                            </option>

                            <option value="botol" {{ old('satuan') == 'botol' ? 'selected' : '' }}>
                                Botol
                            </option>

                            <option value="bungkus" {{ old('satuan') == 'bungkus' ? 'selected' : '' }}>
                                Bungkus
                            </option>

                            <option value="dus" {{ old('satuan') == 'dus' ? 'selected' : '' }}>
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
                            Simpan Produk
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
            input.value = input.value.replace(/\D/g, ''); 
        });
    </script> 

@endsection