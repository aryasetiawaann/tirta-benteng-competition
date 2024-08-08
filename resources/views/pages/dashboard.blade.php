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
                            {{-- <tbody>
                                {{ $counter = 1 }}
                                @foreach ($atlets as $atlet)
                                @foreach ($atlet->acara as $acara)    
                                <tr>
                                    <td>{{ $counter++}}</td>
                                    <td>{{ $atlet->name }}</td>
                                    <td>{{ $acara->kompetisi->nama }}</td>
                                    <td>{{ $acara->nomor_lomba}}</td>
                                    <td><span class="status waiting">{{ $acara->pivot->status_pembayaran }}</span></td>
                                    @if ( now() < $acara->kompetisi->tutup_pendaftaran)
                                    <td><span class="status registration">Menunggu</span></td>
                                    @else
                                    <td><span class="status registration">Selesai</span></td>
                                    @endif
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody> --}}
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
                                                @if (now() < $acara->kompetisi->tutup_pendaftaran)
                                                    <td><span class="status registration">Menunggu</span></td>
                                                @else
                                                    <td><span class="status registration">Selesai</span></td>
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
                        <h2>Aktivitas terbaru</h2>
                    </header>
                    <div class="activity-content">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut in porro dolorum. Minus quasi beatae nam eius neque illo quaerat, tempora, porro debitis libero sunt voluptate ratione harum facere temporibus.</p> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection