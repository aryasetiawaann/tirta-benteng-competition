@extends('layouts.dashboard-layout')
@section('title', 'Dashboard')
@section('content')
    <div class="main-content">
        <div class="top-container">
            <!-- Cards Content -->
            <div class="top-card all-card">
                <div class="card-icon red">
                    <i class='bx bx-swim' ></i>
                </div>
                <div class="card-content">
                    <p>Kompetisi Aktif</p>
                    <h1>{{ $kompetisi_count }}</h1>
                </div>
            </div>
        <div class="top-card all-card">
            <div class="card-icon orange">
                <i class='bx bxs-trophy' ></i>
            </div>
            <div class="card-content">
                <p>Cabang Lomba Terdaftar</p>
                <h1>{{ $acara_count }}</h1>
            </div>
        </div>
        <div class="top-card all-card">
            <div class="card-icon green">
                <i class='bx bxs-wallet' ></i>
            </div>
            <div class="card-content">
                <p>Tagihan Terbayar</p>
                <h1> {{ $tagihanSelesai}} / {{ $totalTagihan }}</h1>
            </div>
        </div>
        <div class="top-card all-card">
            <div class="card-icon">
                <i class='bx bxs-user' ></i>
            </div>
            <div class="card-content">
                <p>Jumlah Atlet</p>
                <h1>{{ $atlet_count}}</h1>
            </div>
        </div>
    </div>

    <!-- Pengumuman jika nomor telepon belum diisi -->
    @if (auth()->user()->phone == null)
        <div class="alert alert-warning">
            <strong>Perhatian!</strong> Anda belum mengisi nomor telepon. Silakan isi nomor telepon anda pada halaman <a href="{{ route('profile.edit') }}">profil</a>
        </div>
    @endif

    <div class="bottom-container">
            <div class="left-container">
                <section class="all-container all-card">
                    <header class="divider">
                        <h1>Daftar Peserta</h1>
                    </header>
                    <div class="table-container">
                        <label for="entries">Tampilkan
                            <select id="entries" name="entries">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select> 
                            entri
                        </label>
                        <input type="text" id="search" placeholder="Cari...">
                        {{-- <div class="loading-overlay" id="loading-overlay">
                            <div class="spinner"></div>
                        </div> --}}
                        <div class="table-scroll">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Kompetisi</th>
                                        <th>Nomor Lomba</th>
                                        <th>Status Pembayaran</th>
                                    </tr>
                                </thead>
                                    <tbody>
                                    @php 
                                        $counter = 1; 
                                        $showMessage = true; // Assume no data first
                                    @endphp

                                    @foreach ($atlets as $atlet)
                                        @foreach ($atlet->acara as $acara)
                                            @if (now() < $acara->kompetisi->waktu_kompetisi)
                                                @php $showMessage = false; @endphp <!-- Set to false when data exists -->
                                                <tr>
                                                    <td>{{ $counter++ }}</td>
                                                    <td>{{ $atlet->name }}</td>
                                                    <td>{{ $acara->kompetisi->nama }}</td>
                                                    <td>{{ $acara->nomor_lomba }}</td>
                                                    <td>
                                                        <span class="status {{ $acara->pivot->status_pembayaran === 'Menunggu' ? 'waiting' : 'bayar' }}">
                                                            {{ $acara->pivot->status_pembayaran }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach

                                    @if ($showMessage)
                                        <tr>
                                            <td colspan="5" style="text-align:center;">Belum ada peserta pada kompetisi aktif</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="pagination">
                            <button class="prev" disabled>Sebelumnya</button>
                            <div class="page-numbers"></div>
                            <button class="next" disabled>Selanjutnya</button>
                        </div>    
                    </div>                            
                </section>
            </div>
            <div class="right-container">
                <div class="all-container all-card">
                    <header class="divider">
                        <h2>Kompetisi Terbaru</h2>
                    </header>
                    @if ($kompetisis->count() > 0)
                        @foreach ($kompetisis as $kompetisi)
                            <div class="news">
                                <h3>{{ $kompetisi->nama }}</h3>
                                @if(now() > $kompetisi->waktu_kompetisi)
                                <span class="status tutup smaller">Selesai</span>
                                @elseif (now() >= $kompetisi->tutup_pendaftaran)
                                <span class="status tutup smaller">Tutup Registrasi</span>
                                @elseif (now() >= $kompetisi->buka_pendaftaran && now() < $kompetisi->tutup_pendaftaran)
                                <span class="status buka smaller">Registrasi</span>
                                @else
                                <span class="status belum-buka smaller">Belum dibuka</span>
                                @endif
                                <p><strong>Lokasi:</strong> {{ $kompetisi->lokasi }}</p>
                                <p>{!! $kompetisi->deskripsi !!}</p>
                            </div>
                            <div class="divider"></div>
                        @endforeach
                    @else
                        <p>Belum ada kompetisi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection