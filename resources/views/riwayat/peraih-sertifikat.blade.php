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
                        <span>{{ $kejuaraan->nama }}</span>
                    </a>
                    <span class="separator">></span>
                    <a href="{{ route('riwayat.sertifikat', $kejuaraan['id']) }}">
                        <span>Sertifikat</span>
                    </a>
                    <span class="separator">></span>
                    <span class="current">{{ $nomorAcara->nama }}</span>
                </nav>

                <div class="page-header">
                    <div class="header-badge">
                        <i class="bx bxs-medal"></i>
                    </div>
                    <h1>Peraih Sertifikat</h1>
                    <p class="event-title">{{ $nomorAcara->nama }}</p>
                    <p class="event-subtitle">{{ $kejuaraan->nama }} | {{ \Carbon\Carbon::parse($kejuaraan->waktu_kompetisi)->format('Y') }}</p>
                </div>
            </div>
        </section>

        <!-- Winners Section -->
        <section class="winners-section">
            <div class="container">
                <div class="winners-container horizontal">
                    @foreach($pemenang as $index => $winner)
                    <div class="winner-card horizontal" data-rank="{{ $winner->rank }}">
                        <div class="card-left">
                            <div class="winner-medal rank-{{ $winner->rank }}">
                                <span>{{ $winner->rank }}</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="winner-details">
                                <h3 class="winner-name">{{ $winner->nama }}</h3>
                                <p class="winner-club">{{ $winner->club }}</p>
                            </div>
                            <div class="winner-actions horizontal">
                                <a href="{{ route('riwayat.detail-sertifikat', $winner->kode) }}" class="btn-view-certificate">
                                    <i class="bx bx-show"></i>
                                    <span>Lihat</span>
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
