<x-guest-layout>
    <section id="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.4)), url('{{ asset('assets/img/Swimpage.png') }}'); background-size: cover; background-position: center;">
        <nav class="navbar">
            <div class="navbar-left">
                <a href="{{ route('main') }}">
                    <img src="{{ asset('assets/img/LogoWebHD.png') }}" alt="Logo" class="logo">
                </a>
            </div>
            <div class="navbar-center">
                <ul class="nav-links">
                    <li><a href="#pengenalan">PENGENALAN</a></li>
                    <li><a href="#jadwal">JADWAL</a></li>
                    <li><a href="#biaya">BIAYA</a></li>
                    {{-- <li><a href="#petunjuk">PETUNJUK</a></li> --}}
                </ul>
            </div>
            <div class="navbar-right">
                <a href="{{ route('login') }}"><button class="btn-login">Masuk</button></a>
                <a href="{{ route('register') }}"><button class="btn-register">Daftar</button></a>
            </div>
            <div class="nav-burger">
                <i class="fa fa-bars"></i>
            </div>
        </nav>
        <div class="sidebar">
            <ul class="sidebar-links">
                <li><i class="fa fa-times sidebar-close"></i></li>
                <li><a href="#pengenalan">PENGENALAN</a></li>
                <li><a href="#jadwal">JADWAL</a></li>
                <li><a href="#biaya">BIAYA</a></li>
                <li><a href="#petunjuk">PETUNJUK</a></li>
            </ul>
            <div class="sidebar-btn">
                <a href="{{ route('login') }}"><button class="btn-login">Masuk</button></a>
                <a href="{{ route('register') }}"><button class="btn-register">Daftar</button></a>
            </div>
        </div>
        <div class="hero-content">
            <h1>Swimming Competition Registration</h1>
            <p>Show your skills in this fun and inspiring swimming competition!</p>
            <div class="hero-buttons">
                <a href="{{ route('login') }}"><button class="btn-competition">Ikuti Kompetisi</button></a>
                {{-- <a href="https://www.instagram.com/tirtabentengsc/" target="_blank" class="fa fa-instagram"></a> --}}
            </div>
        </div>
    </section>

    <section id="pengenalan">
        <div class="pengenalan-img">
            <img src="{{ asset('assets/img/pengenalan.jpg') }}" alt="gambar orang berenang">
        </div>
        <div class="pengenalan-content">
            <div class="custom-subheader">
                <div class="line"></div>
                <h3>Tentang Kompetisi</h3>
                <div class="line"></div>
            </div>
            <h2>Swimming Competition</h2>
                <p>Diadakan setiap tahun, Swimming Competition dirancang untuk menginspirasi atlet muda dan berbakat dalam mengejar mimpi mereka di arena olahraga. Dengan berbagai kategori lomba yang mencakup semua gaya renang dan kelompok umur, kompetisi ini memberikan kesempatan bagi semua orang untuk bersaing dan berkembang.
                <br><br>Bersiaplah untuk menyaksikan pertarungan sengit di kolam renang, serta merasakan kegembiraan dan antusiasme yang membara. Mari bersama-sama menciptakan momen tak terlupakan dan mengukir prestasi terbaik di kejuaraan kali ini! </p>
            </div>
    </section>

    <section id="jadwal">
        <div class="custom-subheader">
            <div class="line"></div>
            <h3>Jadwal</h3>
            <div class="line"></div>
        </div>
        @if ($kompetisis->count() > 0)
            @foreach ($kompetisis as $index => $kompetisi)
            <div class="jadwal-container" id="kompetisi-{{ $index }}" style="{{ $index == 0 ? '' : 'display:none;'}}">
                <h2>{{ $kompetisi->nama }}</h2>
                <div class="jadwal-items">
                    <div class="jadwal-item">
                        <div class="jadwal-item-img">
                            <img src="{{ asset('assets/img/pendaftaran.png') }}" alt="pendaftaran form">
                        </div>
                        <p>{{ \Carbon\Carbon::parse($kompetisi->buka_pendaftaran)->format('d M Y') }} - {{ \Carbon\Carbon::parse($kompetisi->tutup_pendaftaran)->subDay()->format('d M Y') }}</p>
                        <h3>Pendaftaran</h3>
                    </div>
                    <div class="jadwal-item">
                        <div class="jadwal-item-img">
                            <img src="{{ asset('assets/img/tech-meeting.png') }}" alt="technical meeting">
                        </div>
                        <p>{{ $kompetisi->waktu_techmeeting? \Carbon\Carbon::parse($kompetisi->waktu_techmeeting)->format('d M Y') : '-' }}</p>
                        <h3>Technical Meeting</h3>
                    </div>
                    <div class="jadwal-item">
                        <div class="jadwal-item-img">
                            <img src="{{ asset('assets/img/kompetisi.png') }}" alt="piala kompetisi">
                        </div>
                        <p>{{ \Carbon\Carbon::parse($kompetisi->waktu_kompetisi)->format('d M Y') }}</p>
                        <h3>Kompetisi</h3>
                    </div>
                </div>
            </div>
            @endforeach
            @if ($kompetisis->count() > 1)
            <div class="jadwal-navigation">
                <button id="jadPrevBtn">Previous</button>
                <button id="jadNextBtn">Next</button>
            </div>
            @endif
        @else
        <h2>Coming Soon!</h2>
        <div class="jadwal-items"></div>
        @endif
        
    </section>

    <section id="biaya">
        <div class="custom-subheader">
            <div class="line"></div>
            <h3>Biaya</h3>
            <div class="line"></div>
        </div>
        @if ($kompetisis->count() > 0)
        <h2>BIAYA KOMPETISI</h2>
        <div class="event-list">
            <ul>
                @foreach($kompetisis as $index => $kompetisi)
                <li class="{{ $index === 0 ? 'active-event' : '' }}" data-index="{{ $index }}">
                    <p>{{ $kompetisi->nama }}</p>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="price-list">
            @foreach ($kompetisis as $index => $kompetisi)    
                @if ($kompetisi->harga->count() > 0)
                    @foreach ($kompetisi->harga as $hargaIndex => $harga)
                        <div class="price" id="price-{{ $index }}-{{ $hargaIndex }}" style="{{ $index !== 0 ? 'display: none;' : '' }}">
                            <h3>{{ $harga->judul }}</h3>
                            <hr>
                            <h4>Rp.{{ number_format($harga->harga, 2, ',', '.') }}</h4>
                            {!! $harga->deskripsi !!}
                            <a href="{{ route('dashboard.kompetisi') }}"><button>Daftar</button></a>
                        </div>
                    @endforeach
                @else
                <div class="price" id="price-{{ $index }}" style="{{ $index !== 0 ? 'display: none;' : '' }}">
                    <h3>Harga belum tersedia</h3>
                    <hr>
                </div>
                @endif
            @endforeach
        </div>
        @else
        <h2>Coming Soon!</h2>
        @endif
    </section>

    {{-- <section id="petunjuk">
        <div class="petunjuk-img">
            <img src="{{ asset('assets/img/Pool.jpg') }}" alt="kolam">
        </div>
        <div class="petunjuk-content">
            <div class="custom-subheader">
                <div class="line"></div>
                <h3>Petunjuk</h3>
                <div class="line"></div>
            </div>
            <h2>Persyaratan Lomba</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. <br><br>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
    </section> --}}

    @include('components.terms-overlay')
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="{{ asset('assets/img/LogoWebHD.png') }}" alt="Logo" class="footer-logo">
                <p>&#169 Tirta Benteng Swimming Club 2024</p>
            </div>
            <div class="footer-menu">
                <h4>Menu</h4>
                <ul>
                    <li><a href="#pengenalan">Pengenalan</a></li>
                    <li><a href="#jadwal">Jadwal</a></li>
                    <li><a href="#biaya">Biaya</a></li>
                    <li><a href="#petunjuk">Petunjuk</a></li>
                    <li><a href="javascript:void(0)" id="termsLink">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Hubungi Kami</h4>
                <a href="tel:0812312312312"><i class="fa fa-phone"></i><span>0812312312312</span></a>
                <a href="mailto:renangtangerang@gmail.com"><i class="fa fa-envelope"></i><span>info@tirtabentengcomp.com</span></a>
            </div>
        </div>
    </footer>
</x-guest-layout>
