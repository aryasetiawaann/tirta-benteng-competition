    <div class="navbar-mobile">
        <div class="navbar-toggle">
            <i class="ph-bold ph-list"></i>
        </div>
        <div class="navbar-title" data-page-title="{{ Route::currentRouteName() }}"></div>
        <div class="navbar-user">
        <a href="{{ route('profile.edit') }}">
            <img src="{{ !is_null(auth()->user()->foto) ? asset(auth()->user()->foto) : asset('assets/img/blank-profile.png') }}" alt="User Image">
        </a>
        </div>
    </div>

    <div class="sidebar">
    <div class="menu-btn">
        <i class="ph-bold ph-caret-left"></i>
    </div>
    <a href="{{ route('profile.edit') }}">
    <div class="head">
        <div class="user-img">
            <img src="{{ !is_null(auth()->user()->foto) ? asset(auth()->user()->foto) : asset('assets/img/blank-profile.png') }}" alt="User Image">
        </div>
        <div class="user-details">
            <p class="title">User</p>
            <p class="name">{{ Auth::user()->name }}</p>
        </div>
    </div>
    </a>
    <div class="nav">
        <div class="menu">
            <p class="title">Main</p>
            <ul>
                <li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="icon ph-bold ph-house-simple"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('dashboard.atlet.index') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.atlet.index') }}">
                        <i class="icon ph-bold ph-person-simple-swim"></i>
                        <span class="text">Atlet Saya</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('dashboard.kompetisi') || Request::routeIs('dashboard.kompe-saya') ? 'active' : '' }}">
                    <a href="#">
                        <i class="icon ph-bold ph-trophy"></i>
                        <span class="text">Kompetisi</span>
                        <i class="arrow ph-bold ph-caret-down"></i>
                    </a>
                    <ul class="sub-menu {{ Request::routeIs('dashboard.kompetisi') || Request::routeIs('dashboard.kompe-saya') ? 'open' : '' }}">
                        <li class="{{ request()->routeIs('dashboard.kompetisi') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.kompetisi') }}">
                                <span class="text">Daftar</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('dashboard.kompe-saya') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.kompe-saya') }}">
                                <span class="text">Kompetisi Saya</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{ Request::routeIs('dashboard.tagihan') || Request::routeIs('dashboard.tagihan.riwayat') ? 'active' : '' }}">
                    <a href="#">
                        <i class="icon ph-bold ph-wallet"></i>
                        <span class="text">Pembayaran</span>
                        <i class="arrow ph-bold ph-caret-down"></i>
                    </a>
                    <ul class="sub-menu {{ Request::routeIs('dashboard.tagihan') || Request::routeIs('dashboard.tagihan.riwayat') ? 'open' : '' }}">
                        <li class="{{ request()->routeIs('dashboard.tagihan') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.tagihan') }}">
                                <span class="text">Tagihan</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('dashboard.tagihan.riwayat') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.tagihan.riwayat') }}">
                                <span class="text">Riwayat Pembayaran</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{ Request::routeIs('dashboard.bukuacara') || Request::routeIs('dashboard.bukuhasil') ? 'active' : '' }}">
                    <a href="#">
                        <i class="icon ph-bold ph-book-open-text"></i>
                        <span class="text">Unduhan</span>
                        <i class="arrow ph-bold ph-caret-down"></i>
                    </a>
                    <ul class="sub-menu {{ Request::routeIs('dashboard.bukuacara') || Request::routeIs('dashboard.bukuhasil') ? 'open' : '' }}">
                        <li class="{{ request()->routeIs('dashboard.bukuacara') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.bukuacara') }}">
                                <span class="text">Buku Acara</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('dashboard.bukuhasil') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.bukuhasil') }}">
                                <span class="text">Buku Hasil</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="menu">
        <p class="title">Account</p>
        <ul>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href=" {{route('logout') }}"
                        onclick="event.preventDefault();
                        this.closest('form').submit();">
                        <i class="icon ph-bold ph-sign-out"></i>
                        <span class="text">
                            {{ __('Log Out') }}
                        </span>
                    </a>
                </form>
            </li>
        </ul>
    </div>
</div>

<div class="sidebar-backdrop"></div>