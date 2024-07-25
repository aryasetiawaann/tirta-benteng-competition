<div class="container">
      <div class="sidebar">
        <div class="menu-btn">
          <i class="ph-bold ph-caret-left"></i>
        </div>
        <div class="head">
          <div class="user-img">
            <img src="{{ asset('assets/img/Burger.png') }}" alt="" />
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
                    <a href="#">
                      <span class="text">Daftar</span>
                    </a>
                  </li>
                  <li>
                    <a href="#">
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
                    <a href="#">
                      <span class="text">Tagihan</span>
                    </a>
                  </li>
                  <li>
                    <a href="#">
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
                    <a href="#">
                      <span class="text">Buku Acara</span>
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <span class="text">Buku Hasil</span>
                    </a>
                  </li>
                </ul>
              </li>
          </div>
          <div class="menu">
            <p class="title">Settings</p>
            <ul>
              <li>
                <a href="#">
                  <i class="icon ph-bold ph-gear"></i>
                  <span class="text">Settings</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="menu">
          <p class="title">Account</p>
          <ul>
            <li>
              <a href="#">
                <i class="icon ph-bold ph-info"></i>
                <span class="text">Help</span>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="icon ph-bold ph-sign-out"></i>
                <span class="text">Logout</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>