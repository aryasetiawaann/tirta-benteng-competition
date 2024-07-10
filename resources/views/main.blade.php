<x-layout>
    <section id="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.4)), url('{{ asset('assets/img/Swimpage.png') }}'); background-size: cover; background-position: center;">
        <nav class="navbar">
            <div class="navbar-left">
                <img src="{{ asset('assets/img/Burger.png') }}" alt="Logo" class="logo">
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
            <h1>KOMPETISI RENANG TANGERANG</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</p>
            <div class="hero-buttons">
                <button class="btn-competition">Ikuti Kompetisi</button>
                <a href="#" class="fa fa-instagram"></a>
                <a href="#" class="fa fa-facebook"></a>
            </div>
        </div>
    </section>

    <section id="pengenalan">
        <!-- Isi bagian pengenalan -->
    </section>

    <section id="jadwal">
        <!-- Isi bagian jadwal -->
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
