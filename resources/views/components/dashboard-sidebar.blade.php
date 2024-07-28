    <div class="navbar-mobile">
      <div class="navbar-toggle">
        <i class="ph-bold ph-list"></i>
      </div>
      <div class="navbar-title" data-page-title="{{ Route::currentRouteName() }}"></div>
      <div class="navbar-user">
        <img src="{{ asset('assets/img/Seorina.jpeg') }}" alt="User Image">
      </div>
    </div>

    <div class="sidebar">
        <div class="menu-btn">
            <i class="ph-bold ph-caret-left"></i>
        </div>
        <div class="head">
            <div class="user-img">
                <a href="#">
                  <img src="{{ asset('assets/img/Seorina.jpeg') }}" alt="User Image">
                </a>
            </div>
            <div class="user-details">
                <p class="title">User</p>
                <p class="name">Seol In-Ah</p>
            </div>
        </div>
        <div class="nav">
            <div class="menu">
                <p class="title">Main</p>
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}">
                            <i class="icon ph-bold ph-house-simple"></i>
                            <span class="text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.atlet') }}">
                            <i class="icon ph-bold ph-person-simple-swim"></i>
                            <span class="text">Atlet Saya</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="icon ph-bold ph-trophy"></i>
                            <span class="text">Kompetisi</span>
                            <i class="arrow ph-bold ph-caret-down"></i>
                        </a>
                        <ul class="sub-menu">
                            <li>
                                <a href="{{ route('dashboard.daftar') }}">
                                    <span class="text">Daftar</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('dashboard.kompetisi') }}">
                                    <span class="text">Kompetisi Saya</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="icon ph-bold ph-wallet"></i>
                            <span class="text">Pembayaran</span>
                            <i class="arrow ph-bold ph-caret-down"></i>
                        </a>
                        <ul class="sub-menu">
                            <li>
                                <a href="{{ route('dashboard.tagihan') }}">
                                    <span class="text">Tagihan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('dashboard.lunas') }}">
                                    <span class="text">Daftar Pembayaran</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="icon ph-bold ph-book-open-text"></i>
                            <span class="text">Unduhan</span>
                            <i class="arrow ph-bold ph-caret-down"></i>
                        </a>
                        <ul class="sub-menu">
                            <li>
                                <a href="{{ route('dashboard.bukuacara') }}">
                                    <span class="text">Buku Acara</span>
                                </a>
                            </li>
                            <li>
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
                    <a href="#">
                        <i class="icon ph-bold ph-sign-out"></i>
                        <span class="text">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar-backdrop"></div>