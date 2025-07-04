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
                    <a href="{{ route('riwayat.show', $kejuaraan->id) }}">
                        <span>{{ $kejuaraan->nama }}</span>
                    </a>
                    <span class="separator">></span>
                    <span class="current">Hasil Perlombaan</span>
                </nav>

                <div class="page-header">
                    <div class="header-badge">
                        <i class="bx bxs-trophy"></i>
                    </div>
                    <h1>Hasil Perlombaan</h1>
                    <p class="event-title">{{ $kejuaraan->nama }} |
                        {{ \Carbon\Carbon::parse($kejuaraan->waktu_kompetisi)->format('Y') }}</p>
                </div>
            </div>
        </section>

        <!-- PDF Viewer Section -->
        <section class="pdf-viewer-section">
            <div class="container">
                @if ($kejuaraan->file_hasil !== null)
                    <div class="pdf-container">
                        <!-- PDF Embed -->
                        <object data="{{ asset($kejuaraan->file_hasil) }}" type="application/pdf"
                            width="100%" height="100%" class="pdf-object">
                            <div class="pdf-fallback">
                                <p>Browser Anda tidak mendukung tampilan PDF langsung.</p>
                                <a href="{{ asset($kejuaraan->file_hasil) }}" class="btn-download-pdf" download>
                                    <i class="bx bx-download"></i>
                                    <span>Unduh PDF</span>
                                </a>
                            </div>
                        </object>

                        <!-- PDF Controls -->
                        <div class="pdf-controls">
                            <a href="{{ asset($kejuaraan->file_hasil) }}" class="btn-download-pdf"
                                download>
                                <i class="bx bx-download"></i>
                                <span>Unduh Hasil Perlombaan</span>
                            </a>
                            <a href="{{ route('riwayat.show', $kejuaraan->id) }}" class="btn-back">
                                <i class="bx bx-chevron-left"></i>
                                <span>Kembali ke Detail Kejuaraan</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div>
                        <h1 style="text-align: center">Hasil kejuaraan belum tersedia</h1>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-history-layout>
