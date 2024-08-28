@extends('admin.admin-dashboard-layout')
@section('title', 'Atlet Saya')
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card profile-top-card">
                <div class="profile-card-icon">
                    <i class='bx bxs-user' ></i>
                </div>
                <div class="profile-card-content">
                    <p>Profile</p>
                    <h1>{{ auth()->user()->name }}</h1>
                </div>
            </div>
        </div>
        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif
        <div class="profile-bottom-container">
            <section class="profile-section profile-form">
                <div>
                    <img src="{{ !is_null(auth()->user()->foto) ? asset(auth()->user()->foto) : asset('assets/img/blank-profile.png') }}" alt="User Image">
                </div>
                <div>
                    @if (!is_null(auth()->user()->foto))    
                    <a href="/admin/dashboard/profile/delete-foto" onclick="return confirm('Apakah kamu yakin ingin menghapus foto? ')">
                        <button class="delete-photo-button">Hapus Foto</button>
                    </a>
                    @endif
                    <form action="{{route('profile.admin.update')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="file" name="foto" id="foto" accept=".png, .jpeg, .jpg" value="{{ auth()->user()->foto }}">
                        <div>
                            <label for="name">Nama</label>
                            <input id="name" type="text" name="name" value=" {{ auth()->user()->name }} "placeholder="Masukkan nama" >
                        </div>
        
                        <div>
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value=" {{ auth()->user()->email }}" placeholder="Masukkan email">

                        </div>
                        
                        <div>
                            <label for="club">Club</label>
                            <input id="club" type="text" name="club" value="{{ auth()->user()->club }}"  placeholder="Contoh: Tirta Benteng Club">

                        </div>

                        <button type="submit">Simpan</button>
                    </form>
                </div>
            </section>
            <section class="profile-section">
                <h1>Ubah Password</h1>
                <form method="post" action="{{ route('password.update') }}" class="profile-form mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password">Password Aktif</label>
                        <input type="password" name="current_password" id="current_password">
                    </div>

                    <div>
                        <label for="update_password">Password Baru</label>
                        <input type="password" name="password" id="update_password">
                    </div>

                    <div>
                        <label for="confirm_update_password">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="confirm_update_password">
                    </div>
                    
                    <button>Simpan</button>
                </form>
            </section>

            <section class="profile-section profile-delete-section">
                <h1>Hapus Akun</h1>
                <p>Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen.</p>
                <form method="post" action="{{ route('profile.admin.destroy') }}">
                    @csrf
                    @method('delete')

                    <div>
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password">
                        
                        <ul>
                            @foreach ($errors->getBag('userDeletion')->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button>Hapus Akun</button>
                </form>
            </section>
        </div>
    </div>
@endsection
