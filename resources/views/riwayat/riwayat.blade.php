<x-history-layout>
    <div class="riwayat-page">
        <!-- Header Section -->
        <section class="riwayat-header-section">
            <div class="container">
                <nav class="breadcrumb">
                    <a href="{{ route('main') }}">
                        <i class="bx bxs-home"></i>
                        <span>Beranda</span>
                    </a>
                    <span class="separator">></span>
                    <span class="current">Riwayat Kejuaraan</span>
                </nav>

                <div class="page-header">
                    <h1>Riwayat Kejuaraan</h1>
                    <p>Lihat dan unduh dokumen dari kejuaraan yang telah diselenggarakan</p>
                </div>
            </div>
        </section>

        <!-- Kejuaraan List Section -->
        <section class="kejuaraan-list-section">
            <div class="container">
                <div class="filter-section">
                    <div class="filter-controls">
                        <select id="yearFilter" class="filter-select">
                            <option value="">Semua Tahun</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>

                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari kejuaraan...">
                            <i class="bx bx-search"></i>
                        </div>
                    </div>
                </div>

                <div class="kejuaraan-grid" id="kejuaraanGrid">
                    @foreach($kejuaraan as $item)
                    <div class="kejuaraan-card" data-year="{{ $item['year'] }}" data-title="{{ strtolower($item['title']) }}">
                        <div class="card-image">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" onerror="this.src='{{ asset('assets/img/default-event.jpg') }}'">
                            <div class="card-overlay">
                                <span class="year-badge">{{ $item['year'] }}</span>
                            </div>
                        </div>

                        <div class="card-content">
                            <h3 class="card-title">{{ $item['title'] }}</h3>

                            <div class="card-details">
                                <div class="detail-item">
                                    <i class="bx bxs-map"></i>
                                    <span>{{ $item['location'] }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bx bxs-calendar"></i>
                                    <span>{{ $item['date'] }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bx bxs-medal"></i>
                                    <span>{{ $item['type'] }}</span>
                                </div>
                            </div>

                            <div class="card-actions">
                                <a href="{{ route('riwayat.show', $item['id']) }}" class="btn-view-detail">
                                    <i class="bx bx-file"></i>
                                    Lihat Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- No Results Message -->
                <div class="no-results" id="noResults" style="display: none;">
                    <div class="no-results-content">
                        <i class="bx bx-search-alt"></i>
                        <h3>Tidak ada kejuaraan ditemukan</h3>
                        <p>Coba ubah filter atau kata kunci pencarian Anda</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-history-layout>