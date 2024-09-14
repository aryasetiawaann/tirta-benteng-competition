<x-guest-layout>
    <div class="forgot-pass-container">
        <div class="forgot-pass-head">
            <a href="{{ route('login') }}"><i class="fa fa-angle-left" aria-hidden="true"></i><span>Kembali</span></a>
            <div class="forgot-pass-head-logo">
                <img src="{{ asset('assets/img/LogoWebHD.png') }}" alt="logo">
            </div>
            @if (session('status')) 
            <div class="forgot-pass-status-msg">
                <p>{{ session('status') }}</p>
            </div>
            @endif
            <p class="forgot-pass-head-text">Lupa password? Tulis email address anda dan kami akan kirim link reset password ke email anda.</p>
        </div>
        <div class="forgot-pass-body">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="forgot-pass-body-form">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{old('email')}}" required autofocus placeholder="Masukkan email">
                    @error('email')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="forgot-pass-body-bottom">
                    <button>{{ __('Email Password Reset Link') }}</button>
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>