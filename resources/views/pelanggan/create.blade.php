@extends('layouts.app')

@section('title', 'Tambah Pelanggan')

@section('content')

    <div class="container-fluid">

        <div class="mb-4">

            <h2 class="fw-bold mb-1">
                Tambah Pelanggan
            </h2>

            <p class="text-secondary mb-0">
                Tambahkan pelanggan baru ke dalam sistem
            </p>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form action="/pelanggan" method="POST">

                    @csrf


                    <div class="mb-3">

                        <label for="nama_pelanggan" class="form-label fw-semibold">
                            Nama Pelanggan
                        </label>

                        <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}"
                            class="form-control @error('nama_pelanggan') is-invalid @enderror" placeholder="Contoh: Joni">

                        @error('nama_pelanggan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="mb-3">

                        <label for="no_hp" class="form-label fw-semibold">
                            No. HP
                        </label>

                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                            class="form-control @error('no_hp') is-invalid @enderror" maxlength="13" inputmode="numeric"
                            placeholder="Contoh: 08129891212">

                        @error('no_hp')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="mb-4">

                        <label for="alamat" class="form-label fw-semibold">
                            Alamat
                        </label>

                        <textarea id="alamat" name="alamat" rows="4"
                            class="form-control @error('alamat') is-invalid @enderror"
                            placeholder="Masukkan alamat pelanggan">{{ old('alamat') }}</textarea>

                        @error('alamat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="d-flex gap-2">

                        <a href="/pelanggan" class="btn btn-secondary">
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Pelanggan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection