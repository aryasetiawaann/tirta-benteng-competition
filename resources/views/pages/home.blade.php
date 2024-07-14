@section('title') Home @endsection
<x-guest-layout>
    <section id="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.4)), url('{{ asset('img/Swimpage.png') }}'); background-size: cover; background-position: center;">
        <nav class="navbar">
            <div class="navbar-left">
                <a href="/">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
                </a>
            </div>
            <div class="navbar-center">
                <ul class="nav-links">
                    <li><a href="#pengenalan">PENGENALAN</a></li>
                    <li><a href="#jadwal">JADWAL</a></li>
                    <li><a href="#biaya">BIAYA</a></li>
                    <li><a href="#petunjuk">PETUNJUK</a></li>
                </ul>
            </div>
            <div class="navbar-right">
                <button class="btn-login"> <a href="/login">Masuk</a></button>
                <button class="btn-register"><a href="/register">Daftar</a></button>
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
        </div>
        <div class="hero-content">
            <h1>TIRTA BENTENG SWIMMING FUN COMPETITION 2024</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</p>
            <div class="hero-buttons">
                <button class="btn-competition">Ikuti Kompetisi</button>
                <a href="https://www.instagram.com/tirtabentengsc/" target="_blank" class="fa fa-instagram"></a>
                {{-- <a href="#" target="_blank" class="fa fa-facebook"></a> --}}
            </div>
        </div>
    </section>

    <section id="pengenalan">
        <div class="pengenalan-img">
            <img src="{{ asset('img/pengenalan.jpg') }}" alt="gambar orang berenang">
        </div>
        <div class="pengenalan-content">
            <div class="custom-subheader">
                <div class="line"></div>
                <h3>Tentang Kompetisi</h3>
                <div class="line"></div>
            </div>
            <h2>Karya Terbaik, Persada Sesama</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. <br><br>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
    </section>

    <section id="jadwal">
        <div class="custom-subheader">
            <div class="line"></div>
            <h3>Jadwal</h3>
            <div class="line"></div>
        </div>
        <h2>TIRTA BENTENG SWIMMING FUN COMPETITION 2024</h2>
        <div class="jadwal-items">
            <div class="jadwal-item">
                <div class="jadwal-item-img">
                    <img src="{{ asset('img/pendaftaran.png') }}" alt="pendaftaran form">
                </div>
                <p>20 Juni 2024</p>
                <h3>Pendaftaran</h3>
            </div>
            <div class="jadwal-item">
                <div class="jadwal-item-img">
                    <img src="{{ asset('img/tech-meeting.png') }}" alt="technical meeting">
                </div>
                <p>20 Juli 2024</p>
                <h3>Technical Meeting</h3>
            </div>
            <div class="jadwal-item">
                <div class="jadwal-item-img">
                    <img src="{{ asset('img/kompetisi.png') }}" alt="piala kompetisi">
                </div>
                <p>20 Agustus 2024</p>
                <h3>Kompetisi</h3>
            </div>
        </div>
    </section>

    <section id="biaya">
        <div class="custom-subheader">
            <div class="line"></div>
            <h3>Biaya</h3>
            <div class="line"></div>
        </div>
        <h2>BIAYA KOMPETISI</h2>
        <div class="event-list">
            <ul>
                <li class="active-event"><p>Tirta Benteng Swimming Fun Competition 2024</p></li>
                <li><p>Tirta Benteng Swimming Fun Competition 2024</p></li>
                <li><p>Tirta Benteng Swimming Fun Competition 2024</p></li>

            </ul>
        </div>
        <div class="price-list">
            <div class="price">
                <h3>Individu</h3>
                <hr>
                <h4>Rp. 125.000</h4>
                <ul>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                </ul>
                <a href=""><button>Daftar</button></a>
            </div>
            <div class="price">
                <h3>Individu</h3>
                <hr>
                <h4>Rp. 125.000</h4>
                <ul>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                </ul>
                <a href=""><button>Daftar</button></a>
            </div>
            <div class="price">
                <h3>Individu</h3>
                <hr>
                <h4>Rp. 125.000</h4>
                <ul>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                    <li>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing  anjingelit. Dolores, ducimus.</p>
                    </li>
                </ul>
                <a href=""><button>Daftar</button></a>
            </div>
        </div>
    </section>

    <section id="petunjuk">
        <div class="petunjuk-img">
            <img src="{{ asset('img/Pool.jpg') }}" alt="kolam">
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
    </section>

    <footer>
        <div class="footer-container">
                <div class="footer-left">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="footer-logo">
                    <p>	&#169 Tirta Banten Swimming 2024</p>
                </div>
                <div class="footer-menu">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="#pengenalan">Pengenalan</a></li>
                        <li><a href="#jadwal">Jadwal</a></li>
                        <li><a href="#biaya">Biaya</a></li>
                        <li><a href="#petunjuk">Petunjuk</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Hubungi Kami</h4>
                    <a href="tel:0812312312312"><i class="fa fa-phone"></i><span>0812312312312</span></a>
                    <a href="mailto:renangtangerang@gmail.com"><i class="fa fa-envelope"></i><span>renangtangerang@gmail.com</span></a>
                </div>
        </div>
    </footer>
</x-guest-layout>
