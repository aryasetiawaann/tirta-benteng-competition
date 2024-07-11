<x-layout>
    <section id="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.4)), url('{{ asset('assets/img/Swimpage.png') }}'); background-size: cover; background-position: center;">
        <nav class="navbar">
            <div class="navbar-left">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo">
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
                <button class="btn-login">Masuk</button>
                <button class="btn-register">Daftar</button>
            </div>
        </nav>
        <div class="hero-content">
            <h1>TIRTA BENTENG SWIMMING FUN COMPETITION 2024</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</p>
            <div class="hero-buttons">
                <button class="btn-competition">Ikuti Kompetisi</button>
                <a href="https://www.instagram.com/tirtabentengsc/" target="_blank" class="fa fa-instagram"></a>
                <a href="#" target="_blank" class="fa fa-facebook"></a>
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
                    <img src="{{ asset('assets/img/pendaftaran.png') }}" alt="pendaftaran form">
                </div>
                <p>20 Juni 2024</p>
                <h3>Pendaftaran</h3>
            </div>
            <div class="jadwal-item">
                <div class="jadwal-item-img">
                    <img src="{{ asset('assets/img/tech-meeting.png') }}" alt="technical meeting">
                </div>
                <p>20 Juli 2024</p>
                <h3>Technical Meeting</h3>
            </div>
            <div class="jadwal-item">
                <div class="jadwal-item-img">
                    <img src="{{ asset('assets/img/kompetisi.png') }}" alt="piala kompetisi">
                </div>
                <p>20 Agustus 2024</p>
                <h3>Kompetisi</h3>
            </div>
        </div>
    </section>

    <section id="biaya">
        <!-- Isi bagian biaya -->
    </section>

    <section id="petunjuk">
        <!-- Isi bagian petunjuk -->
    </section>

    <footer>
        <!-- Isi footer -->
    </footer>
</x-layout>
