<center>
    <div class="col-md-5 col-lg-5">
        <x-guest-layout>
            <div class="text-center mb-3">
                <h4 class="fw-bold mb-1">
                    Buat Akun
                </h4>

                <p class="text-secondary mb-0">
                    Daftarkan akun anda disini
                </p>
            </div>

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

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="text-start">
                    <div class="mb-3">
                        <label for="" class="form-label">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama"
                            aria-describedby="helpId" value="{{ old('name') }}" required autofocus
                            autocomplete="name" />
                        <small id="helpId" class="text-body-secondary"></small>
                    </div>
                </div>

                <div class="text-start">
                    <div class="mb-3">
                        <label for="" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan Email"
                            aria-describedby="helpId" value="{{ old('email') }}" required autofocus
                            autocomplete="email" />
                        <small id="helpId" class="text-body-secondary"></small>
                    </div>
                </div>

                <div class="text-start">
                    <div class="mb-3">
                        <label for="" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Masukkan Password" aria-describedby="helpId" value="{{ old('password') }}"
                            required autofocus autocomplete="new-password" />
                        <small id="helpId" class="text-body-secondary"></small>
                    </div>
                </div>

                <div class="text-start">
                    <div class="mb-3">
                        <label for="" class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" placeholder="Konfirmasi Password" aria-describedby="helpId" required
                            autofocus autocomplete="new-password" />
                        <small id="helpId" class="text-body-secondary"></small>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-dark">
                        Daftar
                    </button>
                </div>
            </form>

            <div class="text-center">
                <span class="text-secondary">
                    Sudah punya akun?
                </span>
                <a href="{{ route('login')}}" class="text-decoration-none">Login</a>
            </div>

        </x-guest-layout>
    </div>
</center>