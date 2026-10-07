<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'login - Minimarket')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

</head>

<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="col-md-5 col-lg-4"></div>

            <div class="text-center mb-4">
                <div class="bg-dark text-white rounded-3 d-inline-flex align-items-center justify-content-center"
                    style="width: 64px; height: 64px;">

                    <span class="fs-3 fw-bold">
                        M
                    </span>
                </div>

                <h3 class="fw-bold mt-3 mb-1">
                    Minimarket
                </h3>

                <p class="text-secondary mb-0">
                    Sistem Informasi Minimarket
                </p>
            </div>

            <div class="card text-center">
                <div class="card-body">
                    {{ $slot }}
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-secondary">
                    {{ date('Y') }} Minimarket
                </small>
            </div>

        </div>
    </div>

</body>

</html>