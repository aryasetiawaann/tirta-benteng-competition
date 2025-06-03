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
                    <a href="{{ route('riwayat.sertifikat', $kejuaraan['id']) }}">
                        <span>Sertifikat</span>
                    </a>
                    <span class="separator">></span>
                    <span class="current">{{ $nomorAcara }}</span>
                </nav>

                <div class="page-header">
                    <div class="header-badge">
                        <i class="bx bxs-medal"></i>
                    </div>
                    <h1>Peraih Sertifikat</h1>
                    <p class="event-title">{{ $nomorAcara }}</p>
                    <p class="event-subtitle">{{ $kejuaraan['title'] }} | {{ $kejuaraan['year'] }}</p>
                </div>
            </div>
        </section>

        <!-- Winners Section -->
        <section class="winners-section">
            <div class="container">
                <div class="winners-container horizontal">
                    @foreach($pemenang as $index => $winner)
                    <div class="winner-card horizontal" data-rank="{{ $winner['peringkat'] }}">
                        <div class="card-left">
                            <div class="winner-medal rank-{{ $winner['peringkat'] }}">
                                <span>{{ $winner['peringkat'] }}</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="winner-details">
                                <h3 class="winner-name">{{ $winner['nama'] }}</h3>
                                <p class="winner-club">{{ $winner['klub'] }}</p>
                            </div>
                            <div class="winner-actions horizontal">
                                <a href="{{ route('riwayat.view-certificate', ['eventId' => $kejuaraan['id'], 'nomorAcara' => urlencode($nomorAcara)]) }}" class="btn-view-certificate" target="_blank">
                                    <i class="bx bx-show"></i>
                                    <span>Lihat</span>
                                </a>
                                <a href="{{ route('riwayat.view-certificate', ['eventId' => $kejuaraan['id'], 'nomorAcara' => urlencode($nomorAcara)]) }}" class="btn-download-certificate" download>
                                    <i class="bx bx-download"></i>
                                    <span>Unduh</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Back Button -->
                <div class="back-section">
                    <a href="{{ route('riwayat.sertifikat', $kejuaraan['id']) }}" class="btn-back">
                        <i class="bx bx-chevron-left"></i>
                        <span>Kembali ke Daftar Nomor Acara</span>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate winner cards with staggered effect
            const winnerCards = document.querySelectorAll('.winner-card');
            
            winnerCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animated');
                }, 150 * index);
            });
        });
    </script>
</x-history-layout>
