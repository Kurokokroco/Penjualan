<center>
    <div class="col-md-5 col-lg-5 justify-content-center">
        <x-guest-layout>
            <div class="text-center mb-4">

                <h4 class="fw-bold mb-1">
                    Login
                </h4>

                <p class="text-secondary mb-0">
                    Silahkan masuk ke sistem
                </p>
            </div>

            @if (session('status'))

                <div class="alert alert-success">
                    {{ session('status')}}
                </div>

            @endif

            @if ($errors->any())

                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach
                    </ul>
                </div>

            @endif

            <form action="{{ route('login') }}" method="POST">

                @csrf

                <div class="text-start">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control"
                            required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="text-start">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" value="{{ old('password') }}"
                            class="form-control" required autofocus autocomplete="current-password">
                    </div>
                </div>

                <div class="form-check d-flex align-items-center gap-2 p-0">
                    <input class="form-check-input m-0" type="checkbox" id="remember_me" name="remember">
                    <label class="form-check-label" for="remember_me">
                        Ingat Saya
                    </label>
                </div>

                <br>

                <div class="d-grid">
                    <div class="d-grid gap-2">
                        <button type="submit" name="" id="" class="btn btn-dark">
                            Login
                        </button>
                    </div>
                </div>

                <br>

                <div class="text-center">
                    <span class="text-secondary">
                        Belum memiliki account?
                    </span>
                    <a href="{{ route('register')}}" class="text-decoration-none">Daftar</a>
                </div>

            </form>
        </x-guest-layout>
    </div>
</center>