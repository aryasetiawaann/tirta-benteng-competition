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
                    <a href="{{ route('riwayat.index') }}">
                        <span>Riwayat Kejuaraan</span>
                    </a>
                    <span class="separator">></span>
                    <a href="{{ route('riwayat.show', $kejuaraan['id']) }}">
                        <span>{{ $kejuaraan['title'] }}</span>
                    </a>
                    <span class="separator">></span>
                    <span class="current">Sertifikat</span>
                </nav>

                <div class="page-header">
                    <div class="header-badge">
                        <i class="bx bxs-badge-check"></i>
                    </div>
                    <h1>Sertifikat Pemenang</h1>
                    <p class="event-title">{{ $kejuaraan['title'] }} | {{ $kejuaraan['year'] }}</p>
                </div>
            </div>
        </section>

        <!-- Nomor Acara Section -->
        <section class="nomor-acara-section">
            <div class="container">
                <div class="section-header">
                    <h2>Pilih Nomor Acara</h2>
                    <p>Pilih nomor acara untuk melihat sertifikat pemenang</p>
                </div>

                <div class="search-filter">
                    <div class="search-box">
                        <i class="bx bx-search"></i>
                        <input type="text" id="searchNomorAcara" placeholder="Cari nomor acara...">
                    </div>
                </div>

                <div class="nomor-acara-list" id="nomorAcaraList">
                    @foreach($nomorAcara as $index => $acara)
                    <div class="nomor-acara-item" data-acara="{{ strtolower($acara) }}">
                        <div class="acara-info">
                            <div class="acara-number">{{ $index + 1 }}</div>
                            <div class="acara-details">
                                <h3>{{ $acara }}</h3>
                            </div>
                        </div>
                        <div class="acara-actions">
                            <a href="{{ route('riwayat.peraih-sertifikat', ['eventId' => $kejuaraan['id'], 'nomorAcara' => urlencode($acara)]) }}" class="btn-view-sertifikat">
                                <i class="bx bx-medal"></i>
                                <span>Lihat Pemenang</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- No Results Message -->
                <div class="no-results" id="noResultsAcara" style="display: none;">
                    <div class="no-results-content">
                        <i class="bx bx-search-alt"></i>
                        <h3>Tidak ada nomor acara ditemukan</h3>
                        <p>Coba ubah kata kunci pencarian Anda</p>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="back-section">
                    <a href="{{ route('riwayat.show', $kejuaraan['id']) }}" class="btn-back">
                        <i class="bx bx-chevron-left"></i>
                        <span>Kembali ke Detail Kejuaraan</span>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchNomorAcara');
            const nomorAcaraItems = document.querySelectorAll('.nomor-acara-item');
            const noResults = document.getElementById('noResultsAcara');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                nomorAcaraItems.forEach(item => {
                    const acara = item.getAttribute('data-acara');
                    
                    if (acara.includes(searchTerm)) {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (visibleCount === 0) {
                    noResults.style.display = 'block';
                } else {
                    noResults.style.display = 'none';
                }
            });

            // Animate items on load
            nomorAcaraItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('show');
                }, 100 * index);
            });
        });
    </script>
</x-history-layout>
