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
                    <a href="{{ route('riwayat.peraih-sertifikat', ['eventId' => $kejuaraan['id'], 'nomorAcara' => urlencode($nomorAcara)]) }}">
                        <span>{{ $nomorAcara }}</span>
                    </a>
                    <span class="separator">></span>
                    <span class="current">Keterangan Juara</span>
                </nav>

                <div class="page-header">
                    <div class="header-badge">
                        <i class="bx bxs-certification"></i>
                    </div>
                    <h1>Keterangan Juara</h1>
                    <p class="event-title">{{ $nomorAcara }}</p>
                    <p class="event-subtitle">{{ $kejuaraan['title'] }} | {{ $kejuaraan['year'] }}</p>
                </div>
            </div>
        </section>

        <!-- Certificate Detail Section -->
        <section class="certificate-detail-section">
            <div class="container">
                <div class="certificate-card">
                    <div class="certificate-header">
                        <div class="certificate-badge rank-{{ $pemenang['peringkat'] }}">
                            <span>{{ $pemenang['peringkat'] }}</span>
                        </div>
                        <h2 class="certificate-title">{{ $pemenang['nama'] }}</h2>
                    </div>
                    
                    <div class="certificate-details">
                        <div class="detail-row">
                            <div class="detail-label">Certificate ID</div>
                            <div class="detail-value">CERT-{{ $kejuaraan['id'] }}-{{ $pemenang['id'] }}-{{ $kejuaraan['year'] }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Nama</div>
                            <div class="detail-value">{{ $pemenang['nama'] }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Sekolah/Klub</div>
                            <div class="detail-value">{{ $pemenang['klub'] }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Kategori</div>
                            <div class="detail-value">{{ $nomorAcara }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Posisi</div>
                            <div class="detail-value">Juara {{ $pemenang['peringkat'] }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Tim</div>
                            <div class="detail-value">{{ $pemenang['klub'] }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Award</div>
                            <div class="detail-value">Medali {{ $pemenang['peringkat'] == 1 ? 'Emas' : ($pemenang['peringkat'] == 2 ? 'Perak' : 'Perunggu') }}</div>
                        </div>
                    </div>
                    
                    <div class="certificate-actions">
                        <a href="{{ route('riwayat.download-certificate', ['eventId' => $kejuaraan['id'], 'nomorAcara' => urlencode($nomorAcara), 'pesertaId' => $pemenang['id']]) }}" class="btn-download-certificate">
                            <i class="bx bx-download"></i>
                            <span>Unduh Sertifikat</span>
                        </a>
                        <a href="{{ route('riwayat.download-sk', ['eventId' => $kejuaraan['id'], 'nomorAcara' => urlencode($nomorAcara), 'pesertaId' => $pemenang['id']]) }}" class="btn-download-sk">
                            <i class="bx bx-file"></i>
                            <span>Unduh Surat Keterangan</span>
                        </a>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="back-section">
                    <a href="{{ route('riwayat.peraih-sertifikat', ['eventId' => $kejuaraan['id'], 'nomorAcara' => urlencode($nomorAcara)]) }}" class="btn-back">
                        <i class="bx bx-chevron-left"></i>
                        <span>Kembali ke Daftar Pemenang</span>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate certificate card
            setTimeout(() => {
                document.querySelector('.certificate-card').classList.add('animated');
            }, 100);
        });
    </script>
</x-history-layout>