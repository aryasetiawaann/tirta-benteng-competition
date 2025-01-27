<x-guest-layout>
    <div class="register-container">
        <div class="register-head">
            <a href="{{ route('main') }}"><i class="fa fa-angle-left" aria-hidden="true"></i><span>Kembali</span></a>
            <div class="register-head-logo">
                <img src="{{ asset('assets/img/LogoWebHD.png') }}" alt="logo">
            </div>
        </div>
        <div class="register-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="register-body-form">
                    <label for="name">Nama *</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Masukkan nama">
                    @error('name')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="register-body-form">
                    <label for="email">Email *</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Masukkan email">
                    @error('email')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="register-body-form">
                    <label for="phone">Nomor Telepon *</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 081234567890">
                    @error('phone')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="register-body-form">
                    <label for="club">Club/Asal Sekolah (opsional)</label>
                    <input id="club" type="text" name="club" value="{{ old('club') }}" placeholder="Contoh: Tirta Benteng Club">
                    @error('club')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="register-body-form">
                    <label for="password">Password *</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Masukkan password">
                    @error('password')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="register-body-form">
                    <label for="password_confirmation">Konfirmasi Password *</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan ulang password">
                    @error('password_confirmation')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="register-body-bottom">
                    <button>{{ __('Daftar') }}</button>
                    <a class="" href="{{ route('login') }}">
                        {{ __('Sudah memiliki akun?') }}
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>
