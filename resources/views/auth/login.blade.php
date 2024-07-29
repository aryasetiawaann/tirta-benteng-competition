<x-guest-layout>
    <div class="login-container">
        <div class="login-head">
            <a href="{{ route('main') }}"><i class="fa fa-angle-left" aria-hidden="true"></i><span>Kembali</span></a>
            <div class="login-head-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="logo">
            </div>
            @if ($errors->any())
            <div class="login-error-msg">
                <p>Email atau kata sandi salah</p>
            </div>
            @endif
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="login-body-form">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{old('email')}}" required autofocus autocomplete="username" placeholder="Masukkan email">
                </div>
                
                <div class="login-body-form">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                </div>

                <div class="login-body-info">
                    <div class="login-body-info-left">
                        <div class="remember">
                            <input id="remember" type="checkbox" name="remember" value="true">
                            <label for="remember" class="checkbox-label">{{ __('Ingat Saya') }}</label>
                        </div>
                    </div>
                    <div class="login-body-info-right">
                        @if (Route::has('password.request'))
                        <a class="" href="{{ route('password.request') }}">
                            {{ __('Lupa password?') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="login-body-bottom">
                    
                    <button>{{ __('Masuk') }}</button>
                    <a class="" href="{{ route('register') }}">
                        {{ __('Belum memiliki akun?') }}
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>