@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')

    <div class="container-fluid">

        <div class="mb-4">

            <h2 class="fw-bold mb-1">
                Penjualan
            </h2>

            <p class="text-secondary mb-0">
                Kelola transaksi penjualan minimarket
            </p>

        </div>


        @if (session('success'))

            <div class="alert alert-success alert-dismissible fade show" role="alert">

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        @if (session('error'))

            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        <div class="row g-4">


            <div class="col-lg-7">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <h5 class="fw-bold mb-4">
                            Tambah Produk
                        </h5>


                        <form action="/penjualan/tambah-produk" method="POST">

                            @csrf


                            <div class="mb-3">

                                <label for="id_pelanggan" class="form-label fw-semibold">

                                    Pelanggan

                                </label>

                                @php
                                    $pelangganTerpilih = $pelanggan->firstWhere('id', session('id_pelanggan'));
                                @endphp

                                <div class="position-relative" id="pelanggan_combobox">
                                    <input type="text" id="pelanggan_search"
                                        value="{{ $pelangganTerpilih?->nama_pelanggan ?? 'Pelanggan Umum' }}"
                                        class="form-control @error('id_pelanggan') is-invalid @enderror"
                                        placeholder="Ketik nama member..." autocomplete="off">

                                    <input type="hidden" name="id_pelanggan" id="id_pelanggan"
                                        value="{{ session('id_pelanggan') }}">

                                    <div id="pelanggan_options" class="list-group position-absolute w-100 d-none shadow-sm"
                                        style="z-index: 10; max-height: 240px; overflow-y: auto;">

                                        <button type="button" class="list-group-item list-group-item-action" data-value=""
                                            data-label="Pelanggan Umum" data-points="" data-discount="0">
                                            Pelanggan Umum
                                        </button>

                                        @foreach ($pelanggan as $item)
                                            <button type="button" class="list-group-item list-group-item-action"
                                                data-value="{{ $item->id }}" data-label="{{ $item->nama_pelanggan }}"
                                                data-points="{{ $item->poin }}" data-discount="{{ $item->diskonPersen() }}">
                                                {{ $item->nama_pelanggan }}
                                                <small class="d-block text-secondary">
                                                    {{ $item->poin }} poin | Diskon {{ $item->diskonPersen() }}%
                                                </small>
                                            </button>
                                        @endforeach

                                        <div id="pelanggan_empty" class="list-group-item text-secondary d-none">
                                            Member tidak ditemukan.
                                        </div>
                                    </div>
                                </div>

                                <small id="member_benefit" class="form-text">
                                    @if ($pelangganTerpilih)
                                        {{ $pelangganTerpilih->poin }} poin | Diskon {{ $pelangganTerpilih->diskonPersen() }}% |
                                        Transaksi ini menambah 1 poin.
                                    @else
                                        Member memperoleh 1 poin setiap transaksi.
                                    @endif
                                </small>

                                @error('id_pelanggan')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            <div class="mb-3">

                                <label for="id_produk" class="form-label fw-semibold">

                                    Produk

                                </label>

                                <div class="position-relative" id="produk_combobox">
                                    <input type="text" id="produk_search"
                                        class="form-control @error('id_produk') is-invalid @enderror"
                                        placeholder="Ketik kode atau nama produk..." autocomplete="off">

                                    <input type="hidden" name="id_produk" id="id_produk" value="">

                                    <div id="produk_options" class="list-group position-absolute w-100 d-none shadow-sm"
                                        style="z-index: 9; max-height: 240px; overflow-y: auto;">

                                        @foreach ($produk as $item)
                                            <button type="button" class="list-group-item list-group-item-action"
                                                data-value="{{ $item->id }}"
                                                data-search="{{ $item->kode_produk }} {{ $item->nama_produk }}"
                                                data-label="{{ $item->kode_produk }} - {{ $item->nama_produk }} | Rp {{ number_format($item->harga, 0, ',', '.') }} | Stok: {{ $item->stok }}">
                                                {{ $item->kode_produk }} - {{ $item->nama_produk }}
                                                <small class="d-block text-secondary">
                                                    Rp {{ number_format($item->harga, 0, ',', '.') }} | Stok: {{ $item->stok }}
                                                </small>
                                            </button>
                                        @endforeach

                                        <div id="produk_empty" class="list-group-item text-secondary d-none">
                                            Produk tidak ditemukan.
                                        </div>
                                    </div>
                                </div>

                                @error('id_produk')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            <div class="mb-4">

                                <label for="jumlah" class="form-label fw-semibold">

                                    Jumlah

                                </label>

                                <input type="number" name="jumlah" id="jumlah" min="1" value="1"
                                    class="form-control @error('jumlah') is-invalid @enderror">

                                @error('jumlah')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            <button type="submit" class="btn btn-primary">

                                + Tambahkan ke Keranjang

                            </button>

                        </form>

                    </div>

                </div>

            </div>



            <div class="col-lg-5">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <h5 class="fw-bold mb-0">
                                Keranjang
                            </h5>

                            <span class="badge text-bg-primary">
                                {{ count(session('keranjang', [])) }} produk
                            </span>

                        </div>


                        @php
                            $keranjang = session('keranjang', []);
                            $subtotal = 0;

                            foreach ($keranjang as $item) {
                                $subtotal += $item['subtotal'];
                            }

                            $diskonPersen = $pelangganTerpilih?->diskonPersen() ?? 0;
                            $diskon = (int) round($subtotal * $diskonPersen / 100);
                            $total = max(0, $subtotal - $diskon);
                        @endphp


                        @if (count($keranjang) > 0)

                            <form action="/penjualan/batal" method="POST" class="mb-3"
                                onsubmit="return confirm('Batalkan transaksi dan kosongkan keranjang?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    Batal
                                </button>
                            </form>

                            <div class="table-responsive">

                                <table class="table table-sm align-middle">

                                    <thead>

                                        <tr>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Subtotal</th>
                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach ($keranjang as $item)

                                            <tr>

                                                <td>

                                                    <div class="fw-semibold">
                                                        {{ $item['nama_produk'] }}
                                                    </div>

                                                    <small class="text-secondary">
                                                        Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                                    </small>

                                                </td>

                                                <td>
                                                    {{ $item['jumlah'] }}
                                                </td>

                                                <td>
                                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>


                            <hr>


                            <div class="d-flex justify-content-between mb-2">

                                <span>
                                    Subtotal
                                </span>

                                <span>
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </span>

                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span>Diskon member ({{ $diskonPersen }}%)</span>
                                <span>- Rp {{ number_format($diskon, 0, ',', '.') }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-semibold">Total</span>
                                <span class="fw-bold fs-5">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>


                            <form action="/penjualan/simpan" method="POST">

                                @csrf

                                <div class="mb-3">

                                    <label for="pembayaran" class="form-label fw-semibold">

                                        Pembayaran

                                    </label>

                                    <input type="text" id="pembayaran_display"
                                        value="{{ old('pembayaran') ? number_format((int) old('pembayaran'), 0, ',', '.') : '' }}"
                                        inputmode="numeric" class="form-control @error('pembayaran') is-invalid @enderror"
                                        placeholder="Masukkan jumlah pembayaran" autocomplete="off">

                                    <input type="hidden" name="pembayaran" id="pembayaran" value="{{ old('pembayaran') }}">

                                    @error('pembayaran')

                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>

                                    @enderror

                                </div>


                                <button type="submit" class="btn btn-success w-100">

                                    Simpan Transaksi

                                </button>

                            </form>


                        @else

                            <div class="text-center text-secondary py-5">

                                <p class="mb-0">
                                    Keranjang masih kosong.
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>



        <div class="card border-0 shadow-sm mt-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h5 class="fw-bold mb-1">
                            Riwayat Penjualan
                        </h5>

                        <p class="text-secondary mb-0">
                            Daftar transaksi terbaru
                        </p>

                    </div>

                </div>

                <form method="GET" action="/penjualan" class="d-flex gap-2 mb-3">
                    <input type="search" name="search" value="{{ $search }}" class="form-control"
                        placeholder="Cari pelanggan atau tanggal, contoh: 17">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if ($search !== '')
                        <a href="/penjualan" class="btn btn-secondary">Reset</a>
                    @endif
                </form>


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

                                        Belum ada transaksi.

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

@push('scripts')
    <script>
        const customerCombobox = document.getElementById('pelanggan_combobox');
        const customerSearch = document.getElementById('pelanggan_search');
        const customerValue = document.getElementById('id_pelanggan');
        const customerOptions = document.getElementById('pelanggan_options');
        const customerEmpty = document.getElementById('pelanggan_empty');
        const customerBenefit = document.getElementById('member_benefit');
        const productCombobox = document.getElementById('produk_combobox');
        const productSearch = document.getElementById('produk_search');
        const productValue = document.getElementById('id_produk');
        const productOptions = document.getElementById('produk_options');
        const productEmpty = document.getElementById('produk_empty');
        const addProductForm = document.querySelector('form[action="/penjualan/tambah-produk"]');

        if (customerCombobox && customerSearch && customerValue && customerOptions) {
            const customerItems = [...customerOptions.querySelectorAll('button[data-label]')];

            const filterCustomers = () => {
                const search = customerSearch.value.trim().toLowerCase();
                let visibleCount = 0;

                customerItems.forEach((item) => {
                    const matches = item.dataset.label.toLowerCase().includes(search);

                    item.classList.toggle('d-none', !matches);
                    visibleCount += matches ? 1 : 0;
                });

                customerEmpty.classList.toggle('d-none', visibleCount > 0);
                customerOptions.classList.remove('d-none');
            };

            customerSearch.addEventListener('focus', filterCustomers);
            customerSearch.addEventListener('input', () => {
                customerValue.value = '';
                customerBenefit.textContent = 'Member memperoleh 1 poin setiap transaksi.';
                filterCustomers();
            });

            customerItems.forEach((item) => {
                item.addEventListener('click', () => {
                    customerSearch.value = item.dataset.label;
                    customerValue.value = item.dataset.value;
                    customerBenefit.textContent = item.dataset.value
                        ? `${item.dataset.points} poin | Diskon ${item.dataset.discount}% | Transaksi ini menambah 1 poin.`
                        : 'Member memperoleh 1 poin setiap transaksi.';
                    customerOptions.classList.add('d-none');
                });
            });

            document.addEventListener('click', (event) => {
                if (!customerCombobox.contains(event.target)) {
                    customerOptions.classList.add('d-none');
                }
            });

            addProductForm?.addEventListener('submit', (event) => {
                const selectedCustomer = customerSearch.value.trim();
                const isGeneralCustomer = selectedCustomer === 'Pelanggan Umum';

                if (selectedCustomer !== '' && !isGeneralCustomer && customerValue.value === '') {
                    event.preventDefault();
                    customerSearch.focus();
                    alert('Pilih member yang tersedia dari daftar atau pilih Pelanggan Umum.');
                }

                if (productSearch.value.trim() !== '' && productValue.value === '') {
                    event.preventDefault();
                    productSearch.focus();
                    alert('Pilih produk yang tersedia dari daftar.');
                }
            });
        }

        if (productCombobox && productSearch && productValue && productOptions) {
            const productItems = [...productOptions.querySelectorAll('button[data-search]')];

            const filterProducts = () => {
                const search = productSearch.value.trim().toLowerCase();
                let visibleCount = 0;

                productItems.forEach((item) => {
                    const matches = item.dataset.search.toLowerCase().includes(search);

                    item.classList.toggle('d-none', !matches);
                    visibleCount += matches ? 1 : 0;
                });

                productEmpty.classList.toggle('d-none', visibleCount > 0);
                productOptions.classList.remove('d-none');
            };

            productSearch.addEventListener('focus', filterProducts);
            productSearch.addEventListener('input', () => {
                productValue.value = '';
                filterProducts();
            });

            productItems.forEach((item) => {
                item.addEventListener('click', () => {
                    productSearch.value = item.dataset.label;
                    productValue.value = item.dataset.value;
                    productOptions.classList.add('d-none');
                });
            });

            document.addEventListener('click', (event) => {
                if (!productCombobox.contains(event.target)) {
                    productOptions.classList.add('d-none');
                }
            });
        }

        const paymentDisplay = document.getElementById('pembayaran_display');
        const paymentValue = document.getElementById('pembayaran');

        if (paymentDisplay && paymentValue) {
            const formatPayment = (value) => {
                const digits = value.replace(/\D/g, '');

                paymentValue.value = digits;

                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            paymentDisplay.addEventListener('input', (event) => {
                event.target.value = formatPayment(event.target.value);
            });
        }
    </script>
@endpush