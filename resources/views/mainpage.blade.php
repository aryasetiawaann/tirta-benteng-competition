<x-guest-layout>
    <section id="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.4)), url('{{ asset('assets/img/Swimpage.png') }}'); background-size: cover; background-position: center;">
        <nav class="navbar">
            <div class="navbar-left">
                <a href="{{ route('main') }}">
                    <img src="{{ asset('assets/img/SpeedZone2.png') }}" alt="Logo 1" class="logo">
                </a>
            </div>
            <div class="navbar-center">
                <ul class="nav-links">
                    <li><a href="#pengenalan">PENGENALAN</a></li>
                    <li><a href="#riwayat">RIWAYAT</a></li>
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
                <li><a href="#riwayat">RIWAYAT</a></li>
                <li><a href="#jadwal">JADWAL</a></li>
                <li><a href="#biaya">BIAYA</a></li>
                {{-- <li><a href="#petunjuk">PETUNJUK</a></li> --}}
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
                <p>Swimming Competition dirancang untuk menginspirasi atlet muda dan berbakat dalam mengejar mimpi mereka di arena olahraga. Dengan berbagai kategori lomba yang mencakup semua gaya renang dan kelompok umur, kompetisi ini memberikan kesempatan bagi semua orang untuk bersaing dan berkembang.
                <br><br>Bersiaplah untuk menyaksikan pertarungan sengit di kolam renang, serta merasakan kegembiraan dan antusiasme yang membara. Mari bersama-sama menciptakan momen tak terlupakan dan mengukir prestasi terbaik di kejuaraan kali ini! </p>
            </div>
    </section>

    <section id="riwayat">
        <div class="riwayat-container">
            <div class="riwayat-header">
                <div class="custom-center-subheader">
                    <div class="line"></div>
                    <h3>Prestasi</h3>
                    <div class="line"></div>
                </div>
                <h2>Riwayat Perlombaan</h2>
            </div>

            <!-- Looping Carousel Container -->
            <div class="riwayat-carousel-container">
                <button class="carousel-btn carousel-prev" id="riwayatPrevBtn" aria-label="Previous slide">
                    <i class="bx bx-chevron-left"></i>
                </button>
                
                <div class="riwayat-carousel">
                    <div class="riwayat-carousel-track" id="riwayatTrack" role="region" aria-label="Riwayat perlombaan carousel">
                        
                        @foreach ($competition_list as $competition)
                            <div class="riwayat-card carousel-card" data-slide="0">
                                <div class="riwayat-year">{{ \Carbon\Carbon::parse($competition->waktu_kompetisi)->format('Y') }}</div>
                                <h3 class="riwayat-title">{{$competition->nama}}</h3>
                                <div class="riwayat-details">
                                    <div class="riwayat-detail-item">
                                        <i class="bx bxs-location"></i>
                                        <span>{{$competition->lokasi}}</span>
                                    </div>
                                    <div class="riwayat-detail-item">
                                        <i class="bx bxs-calendar"></i>
                                        <span>{{ \Carbon\Carbon::parse($competition->waktu_kompetisi)->translatedFormat('d F Y') }}</span>
                                    </div>
                                    <div class="riwayat-detail-item">
                                        <i class="bx bxs-user"></i>
                                        <span>{{$competition->kategori}}</span>
                                    </div>
                                </div>
                                <a href="{{ route('riwayat.show', $competition->id) }}" class="hasil-acara clickable-hasil" role="button" aria-label="Lihat detail hasil acara">
                                    <div class="hasil-acara-title">Hasil Acara</div>
                                </a>
                            </div>
                        @endforeach

                        <!-- Card 6 - Lihat Selengkapnya -->
                        <div class="riwayat-card carousel-card see-more-card" data-slide="5">
                            <div class="see-more-content">
                                <div class="see-more-icon">
                                    <i class="bx bxs-plus-circle"></i>
                                </div>
                                <h3 class="see-more-title">Lihat Lebih Banyak</h3>
                                <p class="see-more-desc">Jelajahi semua riwayat perlombaan dan prestasi lengkap</p>
                                <button class="btn-see-more-all" onclick="window.location.href='{{ route('riwayat.index') }}'" aria-label="Lihat semua riwayat perlombaan">
                                    Lihat Selengkapnya
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-btn carousel-next" id="riwayatNextBtn" aria-label="Next slide">
                    <i class="bx bx-chevron-right"></i>
                </button>

                <!-- Auto-play Indicator Dots -->
                <div class="carousel-auto-play-indicator" id="riwayatIndicator">
                    <div class="auto-play-dot active" data-slide="0" aria-label="Go to slide 1"></div>
                    <div class="auto-play-dot" data-slide="1" aria-label="Go to slide 2"></div>
                    <div class="auto-play-dot" data-slide="2" aria-label="Go to slide 3"></div>
                    <div class="auto-play-dot" data-slide="3" aria-label="Go to slide 4"></div>
                    <div class="auto-play-dot" data-slide="4" aria-label="Go to slide 5"></div>
                    <div class="auto-play-dot" data-slide="5" aria-label="Go to slide 6"></div>
                </div>
            </div>

            <!-- Tombol Lihat Selengkapnya di bawah carousel -->
            <div class="riwayat-bottom-action">
                <a href="{{ route('riwayat.index') }}" class="btn-lihat-selengkapnya" aria-label="Lihat semua hasil acara">
                    <i class="bx bxs-eye"></i>
                    Lihat Selengkapnya
                </a>
            </div>

            <!-- Statistics Section -->
            <div class="riwayat-stats">
                <div class="stat-item">
                    <span class="stat-number">{{$competition_list->count()}}</span>
                    <div class="stat-label">Total Kompetisi</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">-</span>
                    <div class="stat-label">Medali Emas</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">-</span>
                    <div class="stat-label">Total Medali</div>
                </div>
            </div>
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
        <h2>Coming Soon</h2>
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
        <h2>Coming Soon</h2>
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
                <div class="footer-logos">
                    <img src="{{ asset('assets/img/SpeedZone2.png') }}" alt="Logo 1" class="footer-logo">
                </div>
                <p>&#169 Swimming Competition Registration 2024</p>
            </div>

            <div class="footer-menu">
                <h4>Menu</h4>
                <ul>
                    <li><a href="#pengenalan">Pengenalan</a></li>
                    <li><a href="#jadwal">Jadwal</a></li>
                    <li><a href="#biaya">Biaya</a></li>
                    <li><a href="javascript:void(0)" id="termsLink">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Hubungi Kami</h4>
                <ul>
                    <li><a href="tel:+6287864774431"><i class="fa fa-phone"></i><span>087864774431 - Firza</span></a></li>
                </ul>
            </div>
        </div>
    </footer>
</x-guest-layout>
