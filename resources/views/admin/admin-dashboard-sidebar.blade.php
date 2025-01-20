    <div class="navbar-mobile">
      <div class="navbar-toggle">
        <i class="ph-bold ph-list"></i>
      </div>
      <div class="navbar-title" data-page-title="{{ Route::currentRouteName() }}"></div>
      <div class="navbar-user">
        <img src="{{ !is_null(auth()->user()->foto) ? asset(auth()->user()->foto) : asset('assets/img/blank-profile.png') }}" alt="User Image">
      </div>
    </div>

    <div class="sidebar">
        <div class="menu-btn">
            <i class="ph-bold ph-caret-left"></i>
        </div>
        <a href="{{ route('profile.admin.edit') }}" class="no-underline">
            <div class="head">
                <div class="user-img">
                    <img src="{{ !is_null(auth()->user()->foto) ? asset(auth()->user()->foto) : asset('assets/img/blank-profile.png') }}" alt="User Image">
                </div>
                <div class="user-details">
                    <p class="title">Admin</p>
                    <p class="name">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </a>
        <div class="nav">
            <div class="menu">
                <p class="title">Main</p>
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon ph-bold ph-house-simple"></i>
                            <span class="text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.admin.kompetisi') }}">
                            <i class="icon ph-bold ph-trophy"></i>
                            <span class="text">Tambah Kompetisi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.admin.acara') }}">
                            <i class="icon ph-bold ph-trophy"></i>
                            <span class="text">Tambah Acara</span>
                        </a>
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