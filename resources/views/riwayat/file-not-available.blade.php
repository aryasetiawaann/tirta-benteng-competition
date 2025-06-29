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
                    <span class="current">{{ $title }}</span>
                </nav>

                <div class="page-header">
                    <div class="header-badge">
                        <i class="bx bxs-error-circle"></i>
                    </div>
                    <h1>{{ $title }}</h1>
                </div>
            </div>
        </section>

        <!-- File Not Available Section -->
        <section class="file-not-available-section">
            <div class="container">
                <div class="file-not-available-card">
                    <div class="file-not-available-icon">
                        <i class="bx bx-file-blank"></i>
                    </div>
                    <h2 class="file-not-available-title">File Belum Tersedia</h2>
                    <p class="file-not-available-message">{{ $message }}</p>
                    
                    <!-- Back Button -->
                    <div class="back-section">
                        <a href="{{ $backUrl }}" class="btn-back">
                            <i class="bx bx-chevron-left"></i>
                            <span>Kembali</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate file not available card
            setTimeout(() => {
                document.querySelector('.file-not-available-card').classList.add('animated');
            }, 100);
        });
    </script>
</x-history-layout>