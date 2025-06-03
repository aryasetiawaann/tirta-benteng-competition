<x-history-layout>
    <div class="riwayat-detail-page">
        <!-- Header Section -->
        <section class="detail-header-section">
            <div class="header-pattern-overlay"></div>
            <div class="container">
                <nav class="breadcrumb">
                    <a href="{{ route('main') }}">
                        <i class="bx bxs-home"></i>
                        <span>Beranda</span>
                    </a>
                    <span class="separator">></span>
                    <a href="{{ route('riwayat.index') }}">Riwayat Kejuaraan</a>
                    <span class="separator">></span>
                    <span class="current">{{ $kejuaraan['title'] }}</span>
                </nav>

                <div class="event-header">
                    <div class="event-info">
                        <div class="event-meta">
                            <span class="year-badge">{{ $kejuaraan['year'] }}</span>
                        </div>
                        <h1>{{ $kejuaraan['title'] }}</h1>

                        <div class="event-details">
                            <div class="detail-item">
                                <i class="bx bxs-location-plus"></i>
                                <span>{{ $kejuaraan['location'] }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="bx bxs-calendar-event"></i>
                                <span>{{ $kejuaraan['date'] }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="bx bxs-medal"></i>
                                <span>{{ $kejuaraan['type'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    
        <!-- Document Categories Section -->
        <section class="document-categories-section">
            <div class="container">
                <div class="section-header">
                    <h2>Dokumen Kejuaraan</h2>
                    <p>Pilih kategori dokumen yang ingin Anda lihat</p>
                </div>

                <div class="categories-grid">
                    <!-- Hasil Perlombaan Card -->
                    <div class="category-card hasil-perlombaan-card">
                        <div class="card-icon">
                            <i class="bx bxs-trophy"></i>
                        </div>
                        <div class="card-content">
                            <h3>Hasil Perlombaan</h3>
                            <p>Lihat hasil lengkap dari setiap nomor perlombaan</p>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('riwayat.hasil-perlombaan', $kejuaraan['id']) }}" class="btn-category">
                                <span>Lihat Hasil</span>
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Sertifikat Pemenang Card -->
                    <div class="category-card sertifikat-card">
                        <div class="card-icon">
                            <i class="bx bxs-badge-check"></i>
                        </div>
                        <div class="card-content">
                            <h3>Sertifikat Pemenang</h3>
                            <p>Unduh sertifikat untuk para pemenang kejuaraan</p>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('riwayat.sertifikat', $kejuaraan['id']) }}" class="btn-category">
                                <span>Lihat Sertifikat</span>
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Surat Keterangan Card -->
                    <div class="category-card surat-keterangan-card">
                        <div class="card-icon">
                            <i class="bx bxs-file-doc"></i>
                        </div>
                        <div class="card-content">
                            <h3>Surat Keterangan</h3>
                            <p>Dokumen surat keterangan resmi kejuaraan</p>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('riwayat.surat-keterangan', $kejuaraan['id']) }}" class="btn-category">
                                <span>Lihat Surat</span>
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="back-section">
                    <a href="{{ route('riwayat.index') }}" class="btn-back">
                        <i class="bx bx-chevron-left"></i>
                        <span>Kembali ke Daftar Kejuaraan</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-history-layout>