{{-- <x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
<x-guest-layout>
    <div class="reset-pass-container">
        <div class="reset-pass-head">
            <a href="{{ route('login') }}"><i class="fa fa-angle-left" aria-hidden="true"></i><span>Kembali</span></a>
            <div class="reset-pass-head-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="logo">
            </div>
        </div>
        <div class="reset-pass-body">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="reset-pass-body-form">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{old('email', $request->email)}}" required autocomplete="username" placeholder="Masukkan email">
                    @error('email')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="reset-pass-body-form">
                    <label for="password">Password *</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" autofocus placeholder="Masukkan password">
                    @error('password')
                    <p>{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="reset-pass-body-form">
                    <label for="password_confirmation">Konfirmasi Password </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan ulang password">
                    @error('password_confirmation')
                    <p>{{ $message }}</p>
                    @enderror
                </div>

                <div class="reset-pass-body-bottom">
                    <button>{{ __('Reset Password') }}</button>
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>