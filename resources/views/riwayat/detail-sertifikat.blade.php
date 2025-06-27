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
                    <a href="{{ route('riwayat.sertifikat', $kejuaraan->id) }}">
                        <span>Sertifikat</span>
                    </a>
                    <span class="separator">></span>
                    <a
                        href="{{ route('riwayat.peraih-sertifikat', ['eventId' => $kejuaraan->id, 'nomorAcara' => $nomorAcara->id]) }}">
                        <span>{{ $nomorAcara->nama }}</span>
                    </a>
                    <span class="separator">></span>
                    <span class="current">Keterangan Juara</span>
                </nav>

                <div class="page-header">
                    <div class="header-badge">
                        <i class="bx bxs-certification"></i>
                    </div>
                    <h1>Keterangan Juara</h1>
                    <p class="event-title">{{ $nomorAcara->nama }}</p>
                    <p class="event-subtitle">{{ $kejuaraan->nama }} |
                        {{ \Carbon\Carbon::parse($kejuaraan->waktu_kompetisi)->format('Y') }}</p>
                </div>
            </div>
        </section>

        <!-- Certificate Detail Section -->
        <section class="certificate-detail-section">
            <div class="container">
                <div class="certificate-card">
                    <div class="certificate-header">
                        <div class="certificate-badge rank-{{ $pemenang->rank }}">
                            <span>{{ $pemenang->rank }}</span>
                        </div>
                        <h2 class="certificate-title">{{ $pemenang->nama }}</h2>
                    </div>

                    <div class="certificate-details">
                        <div class="detail-row">
                            <div class="detail-label">Certificate ID</div>
                            <div class="detail-value">{{ urldecode($pemenang->kode) }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Nama</div>
                            <div class="detail-value">{{ $pemenang->nama }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Sekolah/Klub</div>
                            <div class="detail-value">{{ $pemenang->club }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Kategori</div>
                            <div class="detail-value">{{ $pemenang->nomor_lomba }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Posisi</div>
                            <div class="detail-value">Juara {{ $pemenang->rank }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Tim</div>
                            <div class="detail-value">{{ $pemenang->club }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Award</div>
                            <div class="detail-value">Medali
                                {{ $pemenang->rank == 1 ? 'Emas' : ($pemenang->rank == 2 ? 'Perak' : 'Perunggu') }}
                            </div>
                        </div>
                    </div>

                    <div class="certificate-actions">
                        <a href="/storage/{{ $pemenang->certificate->path }}" target="_blank"
                            class="btn-download-certificate">
                            <i class="bx bx-download"></i>
                            <span>Unduh Sertifikat</span>
                        </a>
                        <a href="/storage/{{ $pemenang->letter->path }}" target="_blank" class="btn-download-sk">
                            <i class="bx bx-file"></i>
                            <span>Unduh Surat Keterangan</span>
                        </a>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="back-section">
                    <a href="{{ route('riwayat.peraih-sertifikat', ['eventId' => $kejuaraan->id, 'nomorAcara' => $nomorAcara->id]) }}"
                        class="btn-back">
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
