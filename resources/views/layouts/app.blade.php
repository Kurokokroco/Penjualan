<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Minimarket')
    </title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">


    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

    <div class="d-flex min-vh-100">

        <aside class="bg-dark text-white p-3" style="width: 240px;">

            <h4 class="mb-4">
                Minimarket
            </h4>

            <nav class="nav nav-pills flex-column gap-1">

                <a href="/dashboard"
                    class="nav-link {{ request()->routeIs('dashboard') || request()->path() === '' ? 'active' : 'text-white' }}">
                    Dashboard
                </a>

                @if(auth()->user()->role === 'admin')

                    <a href="/produk" class="nav-link {{ request()->is('produk', 'produk/*') ? 'active' : 'text-white' }}">
                        Produk
                    </a>

                @endif

                <a href="/pelanggan"
                    class="nav-link {{ request()->is('pelanggan', 'pelanggan/*') ? 'active' : 'text-white' }}">
                    Pelanggan
                </a>

                <a href="/penjualan"
                    class="nav-link {{ request()->is('penjualan', 'penjualan/*') ? 'active' : 'text-white' }}">
                    Penjualan
                </a>

                @if(auth()->user()->role === 'admin')

                    <a href="/laporan"
                        class="nav-link {{ request()->is('laporan', 'laporan/*') ? 'active' : 'text-white' }}">
                        Laporan
                    </a>

                @endif

            </nav>

        </aside>


        <div class="flex-grow-1 bg-light">

            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    @yield('title', 'Dashboard')
                </h5>

                <div class="d-flex align-items-center gap-3">

                    <span class="text-secondary">
                        {{ auth()->user()->name }}
                    </span>

                    <span class="badge bg-secondary">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">

                        @csrf

                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            Logout
                        </button>

                    </form>

                </div>

            </header>


            <main class="p-4">

                @yield('content')

            </main>

        </div>

    </div>

    @stack('scripts')

</body>

</html>