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
                <i class="icon ph-bold ph-wallet"></i>
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
    <!-- <div class="nav-page nav-card">
            <p>
                <a href="#">#</a> / 
            </p>
        </div> -->
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
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Kompetisi</th>
                                    <th>Nomor Lomba</th>
                                    <th>Status Pembayaran</th>
                                    <th>Status Kompetisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($atlets->isEmpty())
                                    <tr><td colspan="7" style="text-align:center;">Belum ada data</td></tr>
                                @else
                                    @foreach ($atlets as $atlet)
                                        @foreach ($atlet->acara as $acara)    
                                            <tr>
                                                <td>{{ $counter = isset($counter) ? $counter + 1 : 1 }}</td>
                                                <td>{{ $atlet->name }}</td>
                                                <td>{{ $acara->kompetisi->nama }}</td>
                                                <td>{{ $acara->nomor_lomba }}</td>
                                                <td><span class="status waiting">{{ $acara->pivot->status_pembayaran }}</span></td>
                                                @if (now() > $acara->kompetisi->waktu_kompetisi)
                                                    <td><span class="status registration">Selesai</span></td>
                                                @elseif (now() >= $acara->kompetisi->tutup_pendaftaran)
                                                    <td><span class="status registration">Berjalan</span></td>
                                                @else
                                                    <td><span class="status registration">Menunggu</span></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
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
                            <div>
                                <h3>{{ $kompetisi->nama }}</h3>
                                @if(now() > $kompetisi->waktu_kompetisi)
                                <p><span class="status tutup smaller">Selesai</span></p>
                                @elseif (now() >= $kompetisi->tutup_pendaftaran)
                                <p><span class="status buka smaller">Berjalan</span></p>
                                @elseif (now() >= $kompetisi->buka_pendaftaran && now() < $kompetisi->tutup_pendaftaran)
                                <p><span class="status buka smaller">Registrasi</span></p>
                                @else
                                <p><span class="status buka smaller">Belum dibuka</span></p>
                                @endif
                                <p>Lokasi: {{ $kompetisi->lokasi }}</p>
                                <p>{{ $kompetisi->deskripsi }}</p>
                            </div>
                        @endforeach
                    @else
                    <p>Belum ada kompetisi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection